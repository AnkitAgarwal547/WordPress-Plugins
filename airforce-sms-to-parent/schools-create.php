<?php

function send_sms_airforce() {
    $numbers = $_POST["numbers"];
    $message = $_POST["message"];
    $date = date('Y-m-d H:i:s');
    
    //insert
    if (isset($_POST['send-sms-af-school'])) {
        if(empty($numbers) && empty($message)){
            $error_msg = 'Please Fill All the Fields';
        }
        elseif (empty($numbers) || empty($message)) {
            $error_msg = 'Both Fields are Mandatory';
        }
        else{
            global $wpdb;
            $table_name = $wpdb->prefix . "airforce_sms";
                
            $nums = explode(",", $numbers);
            $msg = $message;
            $sms = send_sms_dean_ariforce($nums, $msg);

            if($sms->status == 'failure'){
                // var_dump($sms);
                foreach($sms->errors as $err){
                    if($err->code == 7){
                        $error_msg .= $err->message .'<br>';
                    }
                    else if($err->code == 51){
                        foreach($sms->warnings as $warn){
                            $error_msg .= $warn->numbers . ' - ' . $warn->message .'<br>';
                        }
                    }
                }
                
                $wpdb->insert(
                    $table_name, //table
                    array('numbers' => $numbers, 'message' => $message, 'status' => $sms->status, 'date' => $date), //data
                    array('%s', '%s') //data format
                );
                $error_msg .= 'Failed to send Message';
            }
            else{
                $success .= 'Your SMS Balance is - <strong>'. $sms->balance .'</strong><br>';
                $wpdb->insert(
                    $table_name, //table
                    array('numbers' => $numbers, 'message' => $message, 'status' => $sms->status, 'date' => $date), //data
                    array('%s', '%s') //data format
                );
                $success.="SMS Sent Successfully !";
            }
        }
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/airforce-sms-to-parent/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Send new message</h2>
        <?php if (isset($success)): ?><div class="updated"><p><?php echo $success; ?></p></div><?php endif; ?>
        <?php if (isset($error_msg)): ?><div class="error"><p><?php echo $error_msg; ?></p></div><?php endif; ?>
        <br>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <table class='wp-list-table widefat fixed' style="width:100%;">
                <tr>
                    <td>
                        <label for="numbers" style="font-weight: 600;"><?php _e( 'Enter Mobile Numbers here comma seprated like <code>919999999999</code> <code>91</code> is the country code following by number' ); ?></label>
					    <br /><br/>
                        <textarea name="numbers" id="numbers" cols="20" rows="5" style="width:100%;border-radius: 5px;padding: 7px 5px;"><?php echo $numbers ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="message" style="font-weight: 600;"><?php _e( 'Enter your message here' ); ?></label>
                        <br /><br/>
                        <textarea name="message" id="message" cols="20" rows="5" style="width:100%;border-radius: 5px;padding: 7px 5px;"><?php echo $message ?></textarea>
                    </td>
                </tr>
            </table>
            <br>
            <input type='submit' name="send-sms-af-school" value='Save' class="button button-primary button-large">
        </form>
    </div>
    <?php
}

//----------- Sending SMS with TextLocal API to group --------------------------
function send_sms_dean_ariforce($nums, $msg){
    
    $options = get_option( 'theme_settings' );
    $text_local_apikey_option =  $options['text_local_api_key'];
    $text_local_sender_id  = $options['text_local_sender_id'];

	$apiKey = urlencode($text_local_apikey_option);
	
	// Message details
	$numbers = $nums;
	$sender = urlencode($text_local_sender_id);
	$message = rawurlencode($msg);
 
	$numbers = implode(',', $numbers);
 
	// Prepare data for POST request
	$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
 
	// Send the POST request with cURL
	$ch = curl_init('https://api.textlocal.in/send/');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close($ch);
	
	// Process your response here
	return json_decode($response);
}
//----------- Sending SMS with TextLocal API to group ends ----------------------