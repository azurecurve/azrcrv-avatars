<?php
/*
	other plugins tab on settings page
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\Avatars;

/**
 * Instructions tab.
 */
$tab_instructions_label = esc_html__( 'Instructions', 'azrcrv-a' );
$tab_instructions       = '
<table class="form-table azrcrv-settings">

	<tr>
	
		<th scope="row" colspan=2 class="azrcrv-settings-section-heading">
			
				<h2 class="azrcrv-settings-section-heading">' . esc_html__( 'Custom Avatar', 'azrcrv-a' ) . '</h2>
			
		</th>

	</tr>

	<tr>
	
		<td scope="row" colspan=2>
		
			<p>' .
				sprintf( esc_html__( 'A custom avatar can be uploaded on the %1$s Settings page located on the %2$s menu.', 'azrcrv-a' ), PLUGIN_NAME, '<strong>azurecurve</strong>' ) . '
					
			</p>
		
			<p>' .
				sprintf( esc_html__( 'Once a custom avatar has been uploaded, it can be selected as the default on the Discussions page on the Settings menu.', 'azrcrv-a' ), 'new text' ) . '
					
			</p>
		
		</td>
	
	</tr>

	<tr>
	
		<th scope="row" colspan=2 class="azrcrv-settings-section-heading">
			
				<h2 class="azrcrv-settings-section-heading">' . esc_html__( 'User Avatar', 'azrcrv-a' ) . '</h2>
			
		</th>

	</tr>

	<tr>
	
		<td scope="row" colspan=2>
		
			<p>' .
				esc_html__( 'Users can set an avatar of their own on their Profile page; administrators can set or change a user\'s avatar on the Edit User page.', 'azrcrv-a' ) . '
					
			</p>
			<p>' .
				esc_html__( 'If no user avatar is specified, the default avatar selected on the Discussions page will be used.', 'azrcrv-a' ) . '
					
			</p>
		
		</td>
	
	</tr>
	
</table>';
