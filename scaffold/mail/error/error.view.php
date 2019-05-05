<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        ::selection {color: #FFFFFF; background: #33363A;}
        #outlook a {padding: 0;}
        table, th, td {-webkit-font-smoothing: antialiased; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-size: 16px; font-weight: 400; line-height: 26px; text-align: left;}
        table, th, td, h1, h2, h3, h4, h5, h6 {font-family: Helvetica, Arial, sans-serif;}
        img {-ms-interpolation-mode: bicubic; border: 0; display: block; height: auto; line-height: 100%; outline: none; text-decoration: none;}
        table {border-collapse: collapse;}

        .row {width: 700px;}
        .row .row {width: 100%;}

        th.menu-item {display: inline;}

        .hover-shrink:hover {transform: scale(0.7);}

        @media only screen and (max-width: 699px) {

            .row {width: 90% !important;}
            .row .row {width: 100% !important;}

            .column {
                box-sizing: border-box;
                display: inline-block !important;
                width: 100% !important;
            }
            .mobile-1  {max-width: 8.33333%;}
            .mobile-2  {max-width: 16.66667%;}
            .mobile-3  {max-width: 25%;}
            .mobile-4  {max-width: 33.33333%;}
            .mobile-5  {max-width: 41.66667%;}
            .mobile-6  {max-width: 50%;}
            .mobile-7  {max-width: 58.33333%;}
            .mobile-8  {max-width: 66.66667%;}
            .mobile-9  {max-width: 75%;}
            .mobile-10 {max-width: 83.33333%;}
            .mobile-11 {max-width: 91.66667%;}
            .mobile-12 {
                padding-right: 30px !important;
                padding-left: 30px !important;
            }

            .mobile-offset-1  {margin-left: 8.33333% !important;}
            .mobile-offset-2  {margin-left: 16.66667% !important;}
            .mobile-offset-3  {margin-left: 25% !important;}
            .mobile-offset-4  {margin-left: 33.33333% !important;}
            .mobile-offset-5  {margin-left: 41.66667% !important;}
            .mobile-offset-6  {margin-left: 50% !important;}
            .mobile-offset-7  {margin-left: 58.33333% !important;}
            .mobile-offset-8  {margin-left: 66.66667% !important;}
            .mobile-offset-9  {margin-left: 75% !important;}
            .mobile-offset-10 {margin-left: 83.33333% !important;}
            .mobile-offset-11 {margin-left: 91.66667% !important;}

            .has-columns {
                padding-right: 20px !important;
                padding-left: 20px !important;
            }

            .has-columns .column {
                padding-right: 10px !important;
                padding-left: 10px !important;
            }

            .mobile-collapsed .column {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            img {
                max-width: 100%;
                width: auto;
                height: auto;
            }

            .mobile-center {
                display: table !important;
                float: none;
                margin-left: auto !important;
                margin-right: auto !important;
            }
            .mobile-text-center {text-align: center !important;}
            .mobile-text-left   {text-align: left !important;}
            .mobile-text-right  {text-align: right !important;}

            .mobile-valign-top  {vertical-align: top !important;}

            .mobile-full-width {
                display: table;
                width: 100%;
            }

            .spacer,
            .divider th                 {height: 30px; line-height: 100% !important; font-size: 100% !important;}
            .mobile-padding-top         {padding-top: 30px !important;}
            .mobile-padding-top-mini    {padding-top: 10px !important;}
            .mobile-padding-bottom      {padding-bottom: 30px !important;}
            .mobile-padding-bottom-mini {padding-bottom: 10px !important;}
            .mobile-margin-top          {margin-top: 30px !important;}
            .mobile-margin-top-mini     {margin-top: 10px !important;}
            .mobile-margin-bottom       {margin-bottom: 30px !important;}
            .mobile-margin-bottom-mini  {margin-bottom: 10px !important;}
        }
    </style>
</head>
<body style="box-sizing: border-box; margin: 0; padding: 0; width: 100%; background: #FFFFFF;">
<?php
//:date, :exception, :message, :file, :line, :trace, 0
?>
<table class="wrapper" align="center" bgcolor="#FFFFFF" style="border-spacing: 0; margin: 0 auto; width: 100%;">
    <tr>
        <td class="spacer" height="15" style="font-size: 15px; line-height: 15px; margin: 0; padding: 0;">&nbsp;</td>
    </tr>
    <tr>
        <td style="padding: 0;">
            <!-- Intro Basic -->
            <table class="row" align="center" bgcolor="#F8F8F8" style="border-spacing: 0; margin: 0 auto;">
                <tr>
                    <td class="spacer" height="40" style="font-size: 40px; line-height: 40px; margin: 0; padding: 0;">&nbsp;</td>
                </tr>
                <tr valign="top" style="vertical-align: top;">
                    <th class="column sans-serif" width="640" style="width: 640px; padding: 0; padding-left: 30px; padding-right: 30px; font-weight: 400; text-align: left;">
                        <h1 class="serif" style="color: #1F2225; font-size: 28px; line-height: 50px; margin: 0; margin-bottom: 30px; padding: 0;"><?php echo \context::get(':exception'); ?> raised</h1>
                        <div style="color: #969AA1; font-size: 18px; line-height: 28px; margin-bottom: 20px;">Message: <?php echo \context::get(':message'); ?></div>
                        <div style="color: #969AA1; font-size: 18px; line-height: 28px; margin-bottom: 20px;">File: <?php echo \context::get(':file'); ?>:<?php echo \context::get(':line'); ?></div>
                        <div style="color: #969AA1; font-size: 18px; line-height: 28px; margin-bottom: 20px;">Date: <?php echo date('Y-m-d H:i:s', \context::get(':date')); ?></div>
                        <div style="color: #969AA1; font-size: 12px; line-height: 16px; margin-bottom: 20px;">Trace:<br /> <?php echo \context::get(':trace'); ?></div>
                    </th>
                </tr>
                <tr>
                    <td class="spacer" height="80" style="font-size: 80px; line-height: 80px; margin: 0; padding: 0;">&nbsp;</td>
                </tr>
            </table>
            <!-- /Intro Basic -->
        </td>
    </tr>
    <tr>
        <td class="spacer" height="15" style="font-size: 15px; line-height: 15px; margin: 0; padding: 0;">&nbsp;</td>
    </tr>
</table>

</body>
</html>