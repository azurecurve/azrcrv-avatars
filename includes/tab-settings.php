<?php
/*
	other plugins tab on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\Avatars;

/**
 * Settings tab.
 */

$tab_settings_label = PLUGIN_NAME . ' ' . esc_html__( 'Settings', 'azrcrv-a' );
$tab_settings       = '
<table class="form-table azrcrv-settings">
		
	<tr>
	
		<th scope="row" colspan="2">
		
			<label for="explanation">
				' . esc_html__( 'Avatars allows the admin to specify a default avatar and allows users to upload their own avatar.', 'azrcrv-a' ) . '
			</label>
			
		</th>
		
	</tr>

	<tr>
	
		<th scope="row">
		
			' . esc_html__( 'Use local avatars only?', 'azrcrv-a' ) . '
			
		</th>
		
		<td>
		
			<fieldset>
				<legend class="screen-reader-text">
						' . esc_html__( 'Use local avatars only?', 'azrcrv-a' ) . '
				</legend>
				
				<label for="localonly">
					<input name="localonly" type="checkbox" id="localonly" value="1" ' . checked( '1', $options['localonly'], false ) . ' />
				</label>
				
			</fieldset>
			
		</td>
		
	</tr>

	<tr>
		<th scope="row">
		
			' . esc_html__( 'Upload custom default avatar?', 'azrcrv-a' ) . '
			
		</th>
		
		<td>

			<p>
			
				<img src="' . esc_url_raw( $options['custom-default-avatar'] ) . '" id="custom-default-avatar-src" style="width: 300px;"><br />
				
				<!-- Outputs the text field and displays the URL of the image retrieved by the media uploader -->
				<input type="hidden" name="custom-default-avatar" id="custom-default-avatar" value="' . esc_url_raw( $options['custom-default-avatar'] ) . '" class="regular-text" />
				
				<input type="button" id="' . PLUGIN_HYPHEN . '-upload-avatar" class="button upload" value="' . esc_html__( 'Upload avatar', 'azrcrv-a' ) . '" />
				<input type="button" id="' . PLUGIN_HYPHEN . '-remove-avatar" class="button remove" value="' . esc_html__( 'Remove avatar', 'azrcrv-a' ) . '" />
				
			</p>
			
			<p>
				<span class="description">' . esc_html__( 'Upload, choose or remove the custom avatar.', 'azrcrv-a' ) . '</span>
			</p>
	
		</td>
		
	</tr>

</table>';
