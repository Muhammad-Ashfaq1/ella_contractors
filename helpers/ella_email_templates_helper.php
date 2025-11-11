<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Ella Contractors Email Templates Helper
 * Contains email HTML templates for appointment reminders
 * 
 * @package EllaContractors
 * @author Custom
 * @version 1.0.0
 */

/**
 * Get client appointment reminder email template
 * 
 * Available merge fields:
 * {appointment_subject}, {appointment_date}, {appointment_time}, 
 * {appointment_location}, {appointment_notes}, {client_name}, 
 * {company_name}, {company_phone}, {company_email}
 * 
 * @return string HTML email template
 */
function ella_get_client_reminder_template()
{
    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 20px; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); padding: 30px; text-align: center;">
            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                Appointment Confirmation
            </h1>
        </div>
        
        <!-- Body -->
        <div style="padding: 40px;">
            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #333333;">
                Dear <strong>{client_name}</strong>,
            </p>
            
            <p style="margin: 0 0 25px; font-size: 16px; line-height: 1.6; color: #333333;">
                This is a confirmation of your upcoming appointment with us. Please find the details below:
            </p>
            
            <!-- Appointment Details Table -->
            <table style="width: 100%; background-color: #f8f9fa; border-radius: 6px; margin-bottom: 25px; border: 1px solid #e9ecef;">
                <tr>
                    <td style="padding: 20px;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 40%; font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057;">
                                    Appointment:
                                </td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #212529;">
                                    {appointment_subject}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057;">
                                    Date:
                                </td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #212529;">
                                    {appointment_date}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057;">
                                    Time:
                                </td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #212529;">
                                    {appointment_time}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; padding: 8px 0; color: #495057;">
                                    Location:
                                </td>
                                <td style="padding: 8px 0; color: #212529;">
                                    {appointment_location}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            
            <!-- Notes (if any) -->
            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #856404;">
                    <strong>Note:</strong> {appointment_notes}
                </p>
            </div>
            
            {presentation_block}
            
            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #333333;">
                A calendar invitation (.ics file) is attached to this email. You can add it to your calendar application (Google Calendar, Outlook, Apple Calendar, etc.).
            </p>
            
            <p style="margin: 0 0 25px; font-size: 16px; line-height: 1.6; color: #333333;">
                If you need to reschedule or have any questions, please contact us at <strong>{company_phone}</strong>.
            </p>
            
            <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #333333;">
                We look forward to meeting with you!
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0 0 10px; font-size: 14px; color: #6c757d;">
                Best regards,<br>
                <strong style="color: #495057;">{company_name}</strong>
            </p>
            <p style="margin: 0; font-size: 12px; color: #868e96;">
                {company_email} | {company_phone}
            </p>
        </div>
        
    </div>
</body>
</html>';
}

/**
 * Get staff appointment reminder email template
 * 
 * Available merge fields:
 * {appointment_subject}, {appointment_date}, {appointment_time},
 * {appointment_location}, {appointment_notes}, {client_name}, 
 * {staff_name}, {company_name}, {crm_link}
 * 
 * @return string HTML email template
 */
function ella_get_staff_reminder_template()
{
    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 20px; font-family: Arial, Helvetica, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); padding: 30px; text-align: center;">
            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">
                📅 Appointment Reminder
            </h1>
        </div>
        
        <!-- Body -->
        <div style="padding: 40px;">
            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #333333;">
                Hi <strong>{staff_name}</strong>,
            </p>
            
            <p style="margin: 0 0 25px; font-size: 16px; line-height: 1.6; color: #333333;">
                This is a reminder about your upcoming appointment. Here are the details:
            </p>
            
            <!-- Appointment Details Table -->
            <table style="width: 100%; background-color: #f8f9fa; border-radius: 6px; margin-bottom: 25px; border: 1px solid #e9ecef;">
                <tr>
                    <td style="padding: 20px;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 40%; font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057;">
                                    Appointment:
                                </td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #212529;">
                                    {appointment_subject}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057;">
                                    Client/Lead:
                                </td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #212529;">
                                    {client_name}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057;">
                                    Date:
                                </td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #212529;">
                                    {appointment_date}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #495057;">
                                    Time:
                                </td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #dee2e6; color: #212529;">
                                    {appointment_time}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; padding: 8px 0; color: #495057;">
                                    Location:
                                </td>
                                <td style="padding: 8px 0; color: #212529;">
                                    {appointment_location}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            
            <!-- Notes (if any) -->
            <div style="background-color: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; margin-bottom: 25px; border-radius: 4px;">
                <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #0c5460;">
                    <strong>Notes:</strong><br>
                    {appointment_notes}
                </p>
            </div>
            
            {presentation_block}
            
            <!-- CTA Button -->
            <div style="text-align: center; margin-bottom: 25px;">
                <a href="{crm_link}" style="display: inline-block; padding: 14px 30px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                    View in CRM
                </a>
            </div>
            
            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #333333;">
                A calendar invitation (.ics file) is attached to this email for your convenience.
            </p>
            
            <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #6c757d; font-style: italic;">
                💡 Pro tip: Make sure you are prepared with any necessary materials before the appointment.
            </p>
        </div>
        
        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0 0 10px; font-size: 14px; color: #6c757d;">
                This is an automated reminder from<br>
                <strong style="color: #495057;">{company_name} CRM</strong>
            </p>
            <p style="margin: 0; font-size: 12px; color: #868e96;">
                Please do not reply to this email. Log into the CRM to manage appointments.
            </p>
        </div>
        
    </div>
</body>
</html>';
}

