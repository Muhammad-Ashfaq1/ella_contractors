<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- SMS Modal -->
<div class="modal fade" id="smsModal" tabindex="-1" role="dialog" aria-labelledby="smsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="smsModalLabel">Send SMS</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <h3>Compose SMS To Lead</h3> 
                        <span class="smalltxt">Customer reply will appear here & in the CRM SMS Panel</span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="lead emoji-picker-container lead_sms_description">
                        <?php echo render_textarea('sms_body_textarea','','',array('placeholder'=>_l('message_body'), 'data-emojiable'=>'true'),array(),'mtop15'); ?>
                    </div>
                    <?php echo get_typos_by_category('sms'); ?>
                    
                    <?php echo form_open_multipart('upload_image/upload',array('class'=>'staff-form','id'=>'imageUploadForm','autocomplete'=>'off')); ?>
                    <label style="float: left; width: 100%;" for="media_image" class="profile-image" id="ImageBrowse">Attach Image (.png, .jpg, .jpeg, .gif)</label>
                    <div class="drop-zone">
                        <span class="drop-zone__prompt">Drop or Click Here to Upload</span>
                        <input type="file" name="media_image" class="form-control drop-zone__input" id="media_image" >
                        <input type="hidden" class="imagesresponse" id="media_url" name="media_url" value=""/>
                        <input type="hidden" name="campaign_type" class="campaign_type" value="sms" />
                    </div>
                    <?php echo form_close(); ?>
                    
                    <div class="text-right" style="float: right;">
                        <div class="savebtntemplate" id="smsVCalanderopenModalButton" style="display: inline-block; width: 240px;">
                            <button class="btn btn-primary" style="background: #9E9E9E; font-size: 15px !important;">Attach vCalendar</button>
                        </div>
                        <button id="lead_send_sms" class="btn btn-info">
                            <?php echo _l('send_sms'); ?>
                            <div id="spinner" class="loader" style="display: none;"></div>
                        </button>
                    </div>
                </div>
                <div class="clearfix"></div>
                <br />
                <div class="emailhistory" style="
                    float: left;
                    width: 100%;
                    background: #333;
                    text-align: center;
                    color: #fff;
                    padding: 7px 0;
                    font-size: 25px;
                    font-weight:bold;
                ">SMS History</div>
                <div id="sms_activity_feed" class="activity-feed" style="float: left; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden fields for SMS data -->
<input type="hidden" id="sms_lead_id" name="sms_lead_id" value="">
<input type="hidden" id="sms_contact_number" name="sms_contact_number" value="">
<input type="hidden" id="sms_sender_id" name="sms_sender_id" value="<?php echo get_staff_user_id(); ?>">
