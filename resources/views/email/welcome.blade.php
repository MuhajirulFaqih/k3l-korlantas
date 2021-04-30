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
                                <h4 style="font-family:'HelveticaNeue-Light','Helvetica Neue Light','Helvetica Neue',Helvetica,Arial,'Lucida Grande',sans-serif;line-height:1.1;color:#000;font-weight:500;font-size:23px;margin:0 0 20px;padding:0">Pendaftaran akun {{ env('APP_MASYARAKAT_NAME') }}</h4>
                                <p style="font-weight:normal;font-size:14px;line-height:1.6;margin:0 0 20px;padding:0">Terimakasih {{ $user->pemilik->nama }},</p>
                                <b style="font-weight:normal;font-size:14px;line-height:1.6;margin:0 0 20px;padding:0">
                                    Anda sudah mendaftar pada aplikasi {{ env('APP_MASYARAKAT_NAME') }} {{ env('APP_KESATUAN') }}. Berikut informasi akun anda:<br>
                                    <b>Kode Verifikasi:</b> {{ $user->kode }}<br>
                                    <b>Email:</b> {{ $user->username }}<br>
                                    @if($user->pemilik->no_telp)
                                            <b>No Handphone:</b> {{ $user->pemilik->no_telp }}<br>
                                    @endif

                                    @if($password)
                                        <b>Password:</b> {{ $password }}<br>
                                    @endif
                                </p>
                                @if($user->pemilik->no_telp)
                                    <p>Kami juga mengirim kode verifikasi melalui sms ke nomor tersebut.</p>
                                @endif
                                <p>Salam,<br>Administrator</p>
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
        </tbody>
    </table>
</div>