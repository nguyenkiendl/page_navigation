<div class="wrap">
	<h1><?php esc_html_e('Page Navigation Option', 'pnav') ?></h1>
	<form method="post" action="">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<?php esc_html_e('Types', 'pnav') ?>
					</th>
					<td>
						<select name="pnav_type">
							<?php foreach($types as $k => $type) {
								$selected = $k==$pnav_type ? "selected" : '';
								echo '<option value="' . $k . '"' . $selected . '>' . $type . '</option>';
							} ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php esc_html_e('Show default', 'pnav') ?>
					</th>
					<td>
						<?php foreach($taxonomies as $k => $tax) : ?>
							<fieldset>
								<label>
									<input type="checkbox" name="pnav_taxonomies[]" 
										value="<?php echo $k ?>"
										<?php echo in_array($k, $pnav_taxonomies) ? "checked" : '' ?>
										>
									<span><?php echo $tax ?></span>
								</label>
							</fieldset>
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<?php esc_html_e('Use', 'pnav') ?>
					</th>
					<td>
						<p>
							Use by default: <br>
							archive, category, index, taxonomy or custom wp-query ... <br>
							Setting use by function <b> page_naviagtion($custom_query) </b> place need show. with <b>custom page template</b>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"></th>
					<td>
						<input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('Lưu thay đổi', 'pnav') ?>">
					</td>
				</tr>
			</tbody>
		</table>
		
	</form>
</div>