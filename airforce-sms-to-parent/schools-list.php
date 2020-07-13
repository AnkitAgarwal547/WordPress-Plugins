<?php

function sent_sms_airforce() {
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/airforce-sms-to-parent/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>All SMS</h2>
        <div class="tablenav top">
            <div class="actions"  style="margin-left: 5px;">
                <a href="<?php echo admin_url('admin.php?page=send_sms_airforce'); ?>" class="button button-primary button-large">Send SMS</a>
            </div>
            <br class="clear">
        </div>
        <br>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . "airforce_sms";

        $rows = $wpdb->get_results("SELECT * from $table_name");
        ?>
        <table class='wp-list-table widefat fixed striped posts' style="width:100%;">
            <tr>
                <th class="manage-column column-author" style="font-weight: 600;border-right: 0.5px;">Numbers</th>
                <th class="manage-column column-author" style="font-weight: 600;border-right: 0.5px;">Message</th>
                <th class="manage-column column-author" style="font-weight: 600;">Date</th>
                <th class="manage-column column-author" style="font-weight: 600;">Status</th>
            </tr>
            <?php 
                if($rows){
                    foreach ($rows as $row) { 
            ?>
                    <tr>
                        <td class="manage-column ss-list-width"><?php echo $row->numbers; ?></td>
                        <td class="manage-column ss-list-width"><?php echo $row->message; ?></td>
                        <td>
                            <?php 
                                $date=date_create($row->date);
                                echo date_format($date,"j-F-Y"); 
                            ?>
                        </td>
                        <td>
                            <?php if($row->status == 'failure'){ ?>
                                <span style=" padding: 5px; background: #dc3232; color: #fff; text-transform: uppercase; border-radius: 4px; ">Failed</span>
                            <?php }
                            else{ ?>
                                <span style=" padding: 5px; background: #46b450; color: #fff; text-transform: uppercase; border-radius: 4px; ">Success</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php
                        }
                    }
                    else{ ?>
                        <tr>
                            <td colspan=3><h3 style="text-align:left;">No Record Found</h3></td>
                        </tr>
                    <?php }
                ?>
        </table>
    </div>
    <?php
}