<?php

use Aireset\Default\Init;
use Aireset\Default\License;

// Exit if accessed directly.
defined('ABSPATH') || exit; ?>

<div id="docs" class="nav-content">
  <table class="form-table">
	
	<tr>
		<td class="d-grid">
			<span class="mb-2"><?php echo esc_html__( 'Talvez precise atualizar a tela:', 'aireset-default' ) ?>
		</td>
	</tr>
	
	<?php if ( Init::get_setting('aireset_default_masks') === 'yes' ): ?>
	<tr class="w-75 mt-5">
		<td>
			<h3 class="h2 mt-0"><?php esc_html_e( 'Documentação das máscaras:', 'aireset-default' ); ?></h3>
			
			<div class="p-3 mb-4 bg-light border rounded">
				<h4 class="fw-bold"><?php esc_html_e( 'Como Funciona?', 'aireset-default' ); ?></h4>
				<p class="mb-1"><?php esc_html_e( 'Para aplicar uma máscara a um campo de formulário (<input>), basta adicionar a classe CSS correspondente na seção "Avançado" do campo no Elementor ou diretamente no HTML.', 'aireset-default' ); ?></p>
				<p><strong><?php esc_html_e( 'Exemplo:', 'aireset-default' ); ?></strong> <?php esc_html_e( 'Para um campo de CPF, adicione a classe:', 'aireset-default' ); ?></p>
				<pre class="m-0"><code class="language-html">&lt;input type="text" class="form-control mascara_cpf"&gt;</code></pre>
			</div>

			<div class="table-responsive">
				<table class="table table-striped table-hover align-middle">
					<thead class="table-light">
						<tr class="p-0 d-table-row">
							<th scope="col"><?php esc_html_e( 'Tipo de Máscara', 'aireset-default' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Classe CSS', 'aireset-default' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Exemplo Prático', 'aireset-default' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'Data', 'aireset-default' ); ?><br><small class="text-muted">DD/MM/AAAA</small></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_data</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_data" placeholder="00/00/0000"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'CEP', 'aireset-default' ); ?><br><small class="text-muted"><?php esc_html_e( 'Código Postal', 'aireset-default' ); ?></small></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_cep</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_cep" placeholder="00000-000"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'CPF', 'aireset-default' ); ?></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_cpf</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_cpf" placeholder="000.000.000-00"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'CNPJ', 'aireset-default' ); ?></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_cnpj</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_cnpj" placeholder="00.000.000/0000-00"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'CPF ou CNPJ (Dinâmico)', 'aireset-default' ); ?></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_cpf_ou_cnpj</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_cpf_ou_cnpj" placeholder="<?php esc_attr_e( 'Digite CPF ou CNPJ', 'aireset-default' ); ?>"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'Celular com DDD', 'aireset-default' ); ?><br><small class="text-muted"><?php esc_html_e( 'Telefone com 9º dígito', 'aireset-default' ); ?></small></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_telefone_nono_digito</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_telefone_nono_digito" placeholder="(00) 00000-0000"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'Telefone Fixo com DDD', 'aireset-default' ); ?></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_telefone_ddd</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_telefone_ddd" placeholder="(00) 0000-0000"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'Placa Veicular', 'aireset-default' ); ?><br><small class="text-muted"><?php esc_html_e( 'Padrão e Mercosul', 'aireset-default' ); ?></small></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_placa</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_placa" placeholder="ABC-1234"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'Monetário (R$)', 'aireset-default' ); ?></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_monetario</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_monetario" placeholder="1.234,56"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'Porcentagem (%)', 'aireset-default' ); ?></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_porcentagem</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_porcentagem" placeholder="15,00%"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'Cartão de Crédito', 'aireset-default' ); ?></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_cartaon</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_cartaon" placeholder="0000-0000-0000-0000"></td>
						</tr>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'Validade do Cartão', 'aireset-default' ); ?><br><small class="text-muted">MM/AA</small></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_cartaod</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_cartaod" placeholder="12/28"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br>
			<small><?php esc_html_e( 'As máscaras são aplicadas utilizando a biblioteca jQuery Mask Plugin.', 'aireset-default' ); ?></small>
		</td>
	</tr>
	<?php endif; ?>

	<!-- <tr class="container-separator"></tr> -->
	
	<?php if ( Init::get_setting('aireset_default_intl_tel_input') === 'yes' ): ?>
	<tr class="w-75 mt-5">
		<td>
			<h3 class="h2 mt-0"><?php esc_html_e( 'Documentação da Número com Bandeiras:', 'aireset-default' ); ?></h3>
			<div class="p-3 mb-4 bg-light border rounded">
				<h4 class="fw-bold"><?php esc_html_e( 'Como Funciona?', 'aireset-default' ); ?></h4>
				<p class="mb-1"><?php esc_html_e( 'Para aplicar uma máscara a um campo de formulário (<input>), basta adicionar a classe CSS correspondente na seção "Avançado" do campo no Elementor ou diretamente no HTML.', 'aireset-default' ); ?></p>
				<p><strong><?php esc_html_e( 'Exemplo:', 'aireset-default' ); ?></strong> <?php esc_html_e( 'Para um campo de CPF, adicione a classe:', 'aireset-default' ); ?></p>
				<pre class="m-0"><code class="language-html">&lt;input type="text" class="form-control mascara_telefone_com_bandeira"&gt;</code></pre>
			</div>

			<div class="table-responsive">
				<table class="table table-striped table-hover align-middle">
					<thead class="table-light">
						<tr class="p-0 d-table-row">
							<th scope="col"><?php esc_html_e( 'Tipo de Máscara', 'aireset-default' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Classe CSS', 'aireset-default' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Exemplo Prático', 'aireset-default' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="p-0 d-table-row">
							<td class="ps-0"><?php esc_html_e( 'Celular com DDD e DDI', 'aireset-default' ); ?><br><small class="text-muted"><?php esc_html_e( 'Telefone com Bandeira', 'aireset-default' ); ?></small></td>
							<td class="ps-0"><span class="badge bg-secondary">mascara_telefone_com_bandeira</span></td>
							<td class="ps-0"><input type="text" class="form-control form-control-sm mascara_telefone_com_bandeira" placeholder="(00) 00000-0000"></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br>
			<small><?php esc_html_e( 'As máscaras são aplicadas utilizando a biblioteca intl-tel-input.', 'aireset-default' ); ?></small>
		</td>
	</tr>
	<?php endif; ?>

  </table>
</div>