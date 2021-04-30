<div bgcolor="#FFFFFF" style="font-family:'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif;width:100%!important;min-height:100%;font-size:14px;color:#404040;margin:0;padding:0">

    <table style="font-family:'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif;max-width:100%;border-collapse:collapse;border-spacing:0;width:100%;background-color:transparent;margin:0;padding:0" bgcolor="#fff">
        <tbody><tr style="margin:0;padding:0">
            <td style="margin:0;padding:0"></td>
            <td style="display:block!important;max-width:600px!important;clear:both!important;margin:0 auto;padding:0">
                <div style="max-width:600px;display:block;border-collapse:collapse;margin:0 auto;padding:15px;border-color:#e7e7e7;border-style:solid;border-width:1px 1px 0">
                    <table style="font-family:'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif;max-width:100%;border-collapse:collapse;border-spacing:0;width:100%;background-color:transparent;margin:0;padding:0" bgcolor="#fff">
                        <tbody><tr style="margin:0;padding:0">
                            <td style="margin:0;padding:0">
                                <img height="75px" src="{{ url('api/upload/mail/logo.png') }}" style="max-width:100%;margin:0;padding:0">
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
            <td style="margin:0;padding:0"></td>
        </tr>
        </tbody>
    </table>

    <table style="font-family:'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif;max-width:100%;border-collapse:collapse;border-spacing:0;width:100%;background-color:transparent;margin:0;padding:0" bgcolor="transparent">
        <tbody>
        <tr style="margin:0;padding:0">
            <td style="margin:0;padding:0"></td>
            <td style="display:block!important;max-width:600px!important;clear:both!important;margin:0 auto;padding:0">
                <div style="max-width:600px;display:block;border-collapse:collapse;margin:0 auto;padding:0;border:0 solid #e7e7e7">
                    <table style="font-family:'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif;max-width:100%;border-collapse:collapse;border-spacing:0;width:100%;background-color:transparent;margin:0;padding:0" bgcolor="#fff">
                        <tbody>
                        <tr style="margin:0;padding:0">
                            <td style="background-color:#181B29!important;line-height:4px;font-size:4px;margin:0;padding:0" height="4" bgcolor="#181B29">&nbsp;</td>
                            <td style="background-color:#544E61!important;line-height:4px;font-size:4px;margin:0;padding:0" height="4" bgcolor="#544E61">&nbsp;</td>
                            <td style="background-color:#59656F!important;line-height:4px;font-size:4px;margin:0;padding:0" height="4" bgcolor="#59656F">&nbsp;</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
            <td style="margin:0;padding:0"></td>
        </tr>
        </tbody>
    </table>

    <table style="font-family:'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif;max-width:100%;border-collapse:collapse;border-spacing:0;width:100%;background-color:transparent;margin:0;padding:0" bgcolor="transparent">
        <tbody><tr style="margin:0;padding:0">
            <td style="margin:0;padding:0"></td>
            <td style="display:block!important;max-width:600px!important;clear:both!important;margin:0 auto;padding:0" bgcolor="#FFFFFF">

                <div style="max-width:600px;display:block;border-collapse:collapse;margin:0 auto;padding:30px 15px;border:1px solid #e7e7e7">
                    <table style="font-family:'Helvetica Neue','Helvetica',Helvetica,Arial,sans-serif;max-width:100%;border-collapse:collapse;border-spacing:0;width:100%;background-color:transparent;margin:0;padding:0" bgcolor="transparent">
                        <tbody><tr style="margin:0;padding:0">
                            <td style="margin:0;padding:0">
                                <h4 style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Helvetica,Arial,'Lucida Grande',sans-serif;line-height:1.1;color:#000;font-weight:500;font-size:23px;margin:0 0 20px;padding:0">Password Reset</h4>
                                <p style="font-weight:normal;font-size:14px;line-height:1.6;margin:0 0 20px;padding:0">Hi {{ $user->pemilik->nama }},</p>
                                <p style="font-weight:normal;font-size:14px;line-height:1.6;margin:0 0 20px;padding:0">
                                    Anda telah meminta reset password<br>
                                    Berikut adalah password baru anda<br>
                                    <b>Email :</b> {{ $user->username }}<br>
                                    <b>Password:</b> {{ $password }}<br>
                                </p>
                                <p style="font-weight:normal;font-size:14px;line-height:1.6;margin:0 0 20px;padding:0">
                                    Segera ubah password ini setelah anda login pada menu setting
                                </p>
                                <p>Terimakasih</p>
                                <p style="font-weight:normal;font-size:14px;line-height:1.6;border-top-style:solid;border-top-color:#d0d0d0;border-top-width:3px;margin:40px 0 0;padding:10px 0 0">
                                    <small style="color:#999;margin:0;padding:0">
                                        Email ini dibuat secara otomatis. Mohon tidak mengirimkan balasan ke email ini.
                                    </small>
                                </p>
                            </td>
                        </tr>
                        </tbody></table>
                </div>
            </td>
            <td style="margin:0;padding:0"></td>
        </tr>
        </tbody></table>

    {{--<table style="max-width:100%;border-collapse:collapse;border-spacing:0;width:100%;clear:both!important;background-color:transparent;margin:0 0 60px;padding:0" bgcolor="transparent">
        <tbody><tr style="margin:0;padding:0">
            <td style="margin:0;padding:0"></td>
            <td style="display:block!important;max-width:600px!important;clear:both!important;margin:0 auto;padding:0">
                <div style="max-width:600px;display:block;border-collapse:collapse;margin:0 auto;padding:20px 15px;border-color:#e7e7e7;border-style:solid;border-width:0 1px 1px">
                    <div style="padding:15px 12px;border-width:2px;border-style:dashed;border-color:#f5dadc;background-color:#fbe3e4">
                        <p style="font-size:12px;line-height:18px;font-weight:400;padding:0px;margin:0px">Hati-hati terhadap pihak yang mengaku dari B-kul, meminta data pribadi. B-kul tidak pernah meminta password dan data pribadi melalui email, pesan pribadi, maupun channel lainnya. Untuk semua email dengan link dari B-kul, pastikan alamat URL di browser sudah di alamat <a href="http://b-kul.com" style="color:#00bcd4" target="_blank">b-kul.com</a> bukan alamat lainnya.</p>
                    </div>
                </div>
            </td>
            <td style="margin:0;padding:0"></td>
        </tr>
        <tr style="margin:0;padding:0">
            <td style="margin:0;padding:0"></td>
            <td style="display:block!important;max-width:600px!important;clear:both!important;margin:0 auto;padding:0">
                <div style="max-width:600px;display:block;border-collapse:collapse;margin:0 auto;padding:20px 15px;border-color:#e7e7e7;border-style:solid;border-width:0 1px 1px">
                    <table style="max-width:100%;border-collapse:collapse;border-spacing:0;width:100%;background-color:transparent;margin:0;padding:0" bgcolor="transparent" width="100%">
                        <tbody style="margin:0;padding:0">
                        <tr style="margin:0;padding:0">
                            <td style="margin:0;padding:0 10px 0 0;width:75%" valign="top">
                                <span style="font-size:12px;margin-bottom:6px;display:inline-block">Download Aplikasi B-kul</span>
                                <div style="text-align:left">
                                    <a style="color:#00bcd4" href="#" target="_blank">
                                        <img class="CToWUd" src="https://ci6.googleusercontent.com/proxy/jZDeHhkGgbF_AT6sU01tjx2fEc0M_4hjEsNng1E966C4_ekj1fxsOmGAO1gm8lYh8-rQFUH0B1fWr04RolRwbTBpB1sbWAPkjht002M=s0-d-e1-ft#https://ecs1.tokopedia.net/img/email/apple-download.png" style="border:0;min-height:auto;max-width:120px;outline:0" alt="Download iOS App">
                                    </a>

                                    <a style="color:#00bcd4" href="" target="_blank">
                                        <img class="CToWUd" src="https://ci6.googleusercontent.com/proxy/wrhw_pD7_rqCfe43C6RCrO1tgcRoOTjxN20wM0WfB8QpRQKlB4NvsjTuuc5stR3BeeB2fR4kZN0xsKeZtzhH5l8-YvHzUsfILODGoysWcg=s0-d-e1-ft#https://ecs1.tokopedia.net/img/email/android-download.png" style="border:0;min-height:auto;max-width:120px;outline:0" alt="Download Android App">
                                    </a>
                                </div>
                            </td>
                            <td style="margin:0;padding:0" valign="top">
                                <span style="font-size:12px;margin-bottom:6px;display:inline-block">Ikuti Kami</span>
                                <div style="text-align:left">
                                    <a href="http://facebook.com/b-kul" style="color:#00bcd4;display:inline-block" target="_blank"><img class="CToWUd" src="https://ci4.googleusercontent.com/proxy/aW2KOB2VNwysUc6SIW7W8ziaKdNpDDkCaTaxlwlI997V4BRKKZ-woLzzcgGHs5paBk7pAs-iGVPqjRIqTIC8vZgLupXnWQ8=s0-d-e1-ft#https://ecs1.tokopedia.net/img/email/facebook.png" alt="Facebook" style="border:0;min-height:auto;max-width:100%;outline:0"></a>
                                    <a href="http://twitter.com/b-kul" style="color:#00bcd4;display:inline-block" target="_blank"><img class="CToWUd" src="https://ci4.googleusercontent.com/proxy/dbZF2r05yvk-C7QMZKwLFxv3fNVjrQDkhv7WJI9CoW0g6n1tw3Ru-xz7Eh2KA6AAU_O3XaAKp2LWTCDo2Gi6DdA3VQqVIQ=s0-d-e1-ft#https://ecs1.tokopedia.net/img/email/twitter.png" alt="Twitter" style="border:0;min-height:auto;max-width:100%;outline:0"></a>
                                    <a href="http://google" style="color:#00bcd4;display:inline-block" target="_blank"><img class="CToWUd" src="https://ci5.googleusercontent.com/proxy/0K-pKWPZ4AQ1QlZ1x9bKl2ocYvzxsMCR3p3hC_bxHUskyt42DQCxcitINestCoOjDPnfMDdlXiS7dsIvJCTsrutOjTbIUXhV5uo=s0-d-e1-ft#https://ecs1.tokopedia.net/img/email/google-plus.png" alt="Google Plus" style="border:0;min-height:auto;max-width:100%;outline:0"></a>
                                    <a href="http://instagram.com/bkul_kuliner" style="color:#00bcd4;display:inline-block" target="_blank"><img class="CToWUd" src="https://ci6.googleusercontent.com/proxy/WIYVP1GvSFxpFxkI6t0UcOAMcMPJVJxvuZQfYoRkSlVvUgZDyvfundP7QS7E6biawJ-J4si5TqYrzQuif6wDFdSCzLmPhxJf=s0-d-e1-ft#https://ecs1.tokopedia.net/img/email/instagram.png" alt="Instagram" style="border:0;min-height:auto;max-width:100%;outline:0"></a>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
            <td style="margin:0;padding:0"></td>
        </tr>
        <tr style="margin:0;padding:0">
            <td style="margin:0;padding:0"></td>
            <td style="display:block!important;max-width:600px!important;clear:both!important;margin:0 auto;padding:0">
                <div style="max-width:600px;display:block;border-collapse:collapse;background-color:#f7f7f7;margin:0 auto;padding:20px 15px;border-color:#e7e7e7;border-style:solid;border-width:0 1px 1px">
                    <table style="max-width:100%;border-collapse:collapse;border-spacing:0;width:100%;background-color:transparent;margin:0;padding:0" bgcolor="transparent" width="100%">
                        <tbody style="margin:0;padding:0">
                        <tr style="margin:0;padding:0">
                            <td style="margin:0;padding:0;width:7%" valign="middle">
                                <img class="CToWUd" src="{{ url('upload/mail/logo.png') }}" style="border:0;min-height:auto;width:41px;outline:0" alt="Mascot B-kul">
                            </td>
                            <td style="margin:0;padding:0;width:53%" valign="middle">
                                <p style="color:#91908e;font-size:10px;line-height:150%;font-weight:normal;margin:0px;padding:0px">
                                    Jika butuh bantuan, gunakan halaman <a href="#" style="color:#0f990f;text-decoration:none;margin:0;padding:0" target="_blank">Kontak Kami</a>.
                                    <br style="margin:0;padding:0">
                                    2016 © B-kul
                                </p>
                            </td>
                            <td style="width:40%" valign="middle">
                                <div style="text-align:right">
                                    <img class="CToWUd" src="{{ url('upload/mail/logo.png') }}" height="50px" alt="B-kul">
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </td>
            <td style="margin:0;padding:0"></td>
        </tr>
        </tbody>
    </table>--}}
</div>