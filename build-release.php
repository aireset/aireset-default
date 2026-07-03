<?php
/**
 * Aireset build + publish.
 * Ofusca as classes de licenca, gera o .zip de release e publica no Elite Licenser
 * (aireset.com.br) via o endpoint privado de deploy.
 *
 * Uso:  php build-release.php            (build + publish)
 *       php build-release.php --no-publish  (so gera o zip em dist/)
 *
 * Chave de deploy: env AIRESET_DEPLOY_KEY  ou  arquivo .aireset-deploy-key na raiz do plugin/pai.
 */

error_reporting( E_ERROR | E_PARSE );

// ===== config deste plugin =====
$PRODUCT_ID    = 1;
$LICENSE_FILES = array( 'includes/classes/class-aireset-license-base.php', 'includes/classes/class-license.php' );
$ENDPOINT      = 'https://aireset.com.br/wp-json/aireset-deploy/v1/publish';
// ================================

$root = __DIR__;
$slug = basename( $root );
$noPublish = in_array( '--no-publish', $argv, true );

// versao a partir do header do arquivo principal
$version = '';
foreach ( glob( $root . '/*.php' ) as $php ) {
	$head = file_get_contents( $php, false, null, 0, 4096 );
	if ( stripos( $head, 'Plugin Name:' ) !== false && preg_match( '/^[\s*]*Version:\s*([0-9][0-9A-Za-z.\-]*)/mi', $head, $m ) ) {
		$version = trim( $m[1] );
		break;
	}
}
if ( '' === $version ) { fwrite( STDERR, "Nao achei a versao no header.\n" ); exit( 1 ); }

// ===== zip com licenca ofuscada =====
$distDir = $root . '/dist';
@mkdir( $distDir, 0777, true );
$zipPath = $distDir . '/' . $slug . '.zip';
if ( file_exists( $zipPath ) ) { unlink( $zipPath ); }

$licSet   = array_flip( $LICENSE_FILES );
$exclDir  = array( '.git', 'node_modules', 'dist' );
$zip = new ZipArchive();
if ( $zip->open( $zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true ) { fwrite( STDERR, "zip fail\n" ); exit( 1 ); }
$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
$obf = 0;
foreach ( $it as $file ) {
	$full = str_replace( '\\', '/', $file->getPathname() );
	$rel  = ltrim( substr( $full, strlen( str_replace( '\\', '/', $root ) ) ), '/' );
	foreach ( $exclDir as $ed ) { if ( preg_match( '#(^|/)' . preg_quote( $ed, '#' ) . '(/|$)#', $rel ) ) { continue 2; } }
	if ( $file->isDir() ) { continue; }
	if ( preg_match( '/\.zip$/i', $rel ) || $rel === 'build-release.php' ) { continue; }
	$entry = $slug . '/' . $rel;
	if ( isset( $licSet[ $rel ] ) ) {
		$ob = obfuscatePhpFile( $full );
		if ( '' === $ob ) { fwrite( STDERR, "obfuscacao falhou: $rel\n" ); exit( 1 ); }
		$zip->addFromString( $entry, $ob ); $obf++;
	} else {
		$zip->addFile( $full, $entry );
	}
}
$zip->close();
echo "zip: $zipPath  (v$version, licenca ofuscada: $obf)\n";

if ( $noPublish ) { echo "--no-publish: parou aqui.\n"; exit( 0 ); }

// ===== publish =====
$key = getenv( 'AIRESET_DEPLOY_KEY' );
if ( ! $key ) {
	foreach ( array( $root . '/.aireset-deploy-key', dirname( $root ) . '/.aireset-deploy-key', dirname( dirname( $root ) ) . '/.aireset-deploy-key' ) as $kf ) {
		if ( is_file( $kf ) ) { $key = trim( file_get_contents( $kf ) ); break; }
	}
}
if ( ! $key ) { fwrite( STDERR, "Sem chave de deploy (env AIRESET_DEPLOY_KEY ou .aireset-deploy-key).\n" ); exit( 1 ); }

$ch = curl_init( $ENDPOINT );
curl_setopt_array( $ch, array(
	CURLOPT_POST           => true,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT        => 180,
	CURLOPT_HTTPHEADER     => array( 'X-Aireset-Deploy-Key: ' . $key ),
	CURLOPT_POSTFIELDS     => array(
		'product_id' => $PRODUCT_ID,
		'version'    => $version,
		'slug'       => $slug,
		'changelog'  => 'Release ' . $version,
		'package'    => new CURLFile( $zipPath, 'application/zip', $slug . '.zip' ),
	),
) );
$resp = curl_exec( $ch );
$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
curl_close( $ch );
echo "publish HTTP $code: $resp\n";
exit( $code >= 200 && $code < 300 ? 0 : 1 );

// ===== obfuscador (identico ao expresso/checkout) =====
function obfuscatePhpFile( string $path ): string {
	$s = file_get_contents( $path ); if ( $s === false || $s === '' ) return '';
	if ( aireset_has_transitions( $path ) ) { $payload = trim( $s ); }
	else { $st = aireset_strip_tags( $s ); if ( $st === '' ) return ''; $mn = aireset_minify( $st ); if ( $mn === '' ) return ''; $payload = '<?php ' . $mn; }
	$inner = aireset_inner_loader( base64_encode( $payload ) );
	return aireset_outer_loader( base64_encode( gzcompress( $inner, 9 ) ) );
}
function aireset_has_transitions( string $p ): bool { $s = file_get_contents( $p ); if ( $s === false || $s === '' ) return false; $st = aireset_strip_tags( $s ); if ( $st === '' ) return false; return str_contains( $st, '?>' ) || preg_match( '/<\?(php|=)?/i', $st ) === 1; }
function aireset_strip_tags( string $s ): string { $s = trim( $s ); if ( str_starts_with( strtolower( $s ), '<?php' ) ) $s = substr( $s, 5 ); elseif ( str_starts_with( $s, '<?' ) ) $s = substr( $s, 2 ); $s = trim( $s ); if ( str_ends_with( $s, '?>' ) ) $s = substr( $s, 0, -2 ); return trim( $s ); }
function aireset_inner_loader( string $p ): string { $d = aireset_rv(); $ds = aireset_rv(); $pv = aireset_rv(); $l = array( '$' . $ds . '=' . var_export( base64_encode( 'base64_decode' ), true ) . ';', '$' . $d . '=base64_decode($' . $ds . ');', '$' . $pv . '=' . var_export( $p, true ) . ';', 'eval("?>".@$' . $d . '($' . $pv . '));' ); return implode( '', aireset_noise( $l ) ); }
function aireset_outer_loader( string $p ): string { $d = aireset_rv(); $ds = aireset_rv(); $inf = aireset_rv(); $ins = aireset_rv(); $pv = aireset_rv(); $rn = aireset_rv(); $cd = aireset_rv(); $l = array( '$' . $ds . '=' . var_export( base64_encode( 'base64_decode' ), true ) . ';', '$' . $d . '=base64_decode($' . $ds . ');', '$' . $ins . '=' . var_export( base64_encode( 'gzuncompress' ), true ) . ';', '$' . $inf . '=@$' . $d . '($' . $ins . ');', '$' . $pv . '=' . var_export( $p, true ) . ';', '$' . $rn . '=function($__b__){eval($__b__);};', '$' . $cd . '=@$' . $inf . '(@$' . $d . '($' . $pv . '));', '$' . $rn . '($' . $cd . ');' ); return '<?php ' . implode( '', aireset_noise( $l ) ); }
function aireset_noise( array $l ): array { $n = array(); foreach ( $l as $x ) $n[] = aireset_rc() . $x . aireset_rc(); return $n; }
function aireset_minify( string $src ): string { $t = token_get_all( '<?php ' . $src ); $o = ''; $pw = false; foreach ( $t as $i => $tk ) { if ( $i === 0 ) continue; if ( is_string( $tk ) ) { $o .= $tk; $pw = false; continue; } [$id,$tx] = $tk; if ( $id === T_COMMENT || $id === T_DOC_COMMENT ) continue; if ( $id === T_WHITESPACE ) { if ( $pw ) continue; $o .= ' '; $pw = true; continue; } $o .= $tx; $pw = false; } $o = preg_replace( '/\s*([{}();,:=\[\]])\s*/', '$1', $o ); $o = preg_replace( '/\s*(=>)\s*/', '$1', $o ); $o = preg_replace( '/\s+/', ' ', $o ); return is_string( $o ) ? trim( $o ) : ''; }
function aireset_rv(): string { $a = 'abcdefghijklmnopqrstuvwxyz'; $n = random_int( 8, 18 ); $b = ''; for ( $i = 0; $i < $n; $i++ ) $b .= $a[ random_int( 0, 25 ) ]; return '_' . $b; }
function aireset_rc(): string { $a = 'abcdefghijklmnopqrstuvwxyz0123456789'; $n = random_int( 4, 14 ); $b = ''; for ( $i = 0; $i < $n; $i++ ) $b .= $a[ random_int( 0, 35 ) ]; return '/*' . $b . '*/'; }
