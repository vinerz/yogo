<?php
/*
 * Mail Plugin Exclusive Configuration
 */

define("MAIL_USE_SMTP", false); /* Defines if you are using or not a SMTP server to send e-mails. */

define("MAIL_SMTP_AUTH",   false); /* Set to true if you need authenticantion in your SMTP server */
define("MAIL_SMTP_SECURE", "ssl"); /* Put your conection encryption mode, it may be ssl or tls, leave blank for none */
define("MAIL_SMTP_HOST",   "smtp.gmail.com"); /* Your SMTP host */
define("MAIL_SMTP_PORT",   465); /* Your SMTP port */
define("MAIL_SMTP_USER",   ""); /* Your SMTP username */
define("MAIL_SMTP_PASS",   ""); /* Your SMTP password */


define("MAIL_FROM_NAME",     "Default App"); /* Put here your sender e-mail */
define("MAIL_FROM_EMAIL",    "admin@app.com"); /* Put here your name for the sender */
define("MAIL_REPLYTO_NAME",  "Default App"); /* Put here your reply-to name */
define("MAIL_REPLYTO_EMAIL", "contact@app.com"); /* YOur reply-to e-mail */

define("MAIL_IS_HTML",  true); /* Set to true if you need HTML at the mail content */
define("MAIL_ALT_BODY", ""); /* The message to show when the user is unable to see the HTML content */
