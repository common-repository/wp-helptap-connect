<div class="wrap">
    <h2>WP HelpTap Connect</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('wp_helptap_connect-group'); ?>
        <?php @do_settings_fields('wp_helptap_connect-group'); ?>

        <table class="form-table">  
            <?php
                $helptap_options = get_option( 'helptap_options' );
                if( !is_array($helptap_options) ) {
                    $helptap_options = array(
                        'tapname' => '',
                        'placement' => 'r_b'
                    );
                }
            ?>
            <tr valign="top">
                <th scope="row"><label for="wp_helptap_connect_tapname">Tapname</label></th>
                <td>
                    <input required type="text" name="helptap_options[tapname]" id="wp_helptap_connect_tapname" value="<?php echo $helptap_options['tapname']; ?>" />
                </td>
            </tr>
            <tr valign="top" style="display:none;">
                <th scope="row"><label for="wp_helptap_connect_placement">Position</label></th>
                <td>
                    <select required name="helptap_options[placement]" id="wp_helptap_connect_placement">
                        <option value='r_b' <?php selected( $helptap_options['placement'], 'r_b' ); ?>>Right Bottom Corner</option>
                    </select>
                </td>
            </tr>
        </table>

        <?php @submit_button(); ?>
    </form>
</div>

