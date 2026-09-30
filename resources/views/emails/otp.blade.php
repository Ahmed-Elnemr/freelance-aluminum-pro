@php
    $isRtl = app()->getLocale() === 'ar';
    $direction = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'right' : 'left';
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $direction }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f1ee;font-family:Tahoma,Arial,sans-serif;color:#1c1917;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f1ee;padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background-color:#ffffff;border:1px solid #e7e5e4;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="height:6px;background-color:#111111;font-size:0;line-height:0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:36px 32px 8px;">
                            <img src="{{ $logoUrl }}" alt="{{ $appName }}" width="72" height="72" style="display:block;width:72px;height:72px;border:0;border-radius:16px;">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:12px 32px 0;font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#78716c;">
                            {{ $appName }}
                        </td>
                    </tr>
                    <tr>
                        <td align="{{ $align }}" style="padding:28px 32px 0;font-size:22px;line-height:1.4;font-weight:700;color:#111111;">
                            {{ $heading }}
                        </td>
                    </tr>
                    <tr>
                        <td align="{{ $align }}" style="padding:12px 32px 0;font-size:15px;line-height:1.7;color:#44403c;">
                            {{ $intro }}
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:28px 32px 8px;">
                            <div dir="ltr" style="display:inline-block;background-color:#111111;color:#ffffff;border-radius:12px;padding:16px 28px;font-size:32px;line-height:1;letter-spacing:10px;font-weight:700;">
                                {{ $otp }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="{{ $align }}" style="padding:8px 32px 0;font-size:13px;line-height:1.6;color:#78716c;">
                            {{ $expires }}
                        </td>
                    </tr>
                    <tr>
                        <td align="{{ $align }}" style="padding:20px 32px 32px;font-size:13px;line-height:1.6;color:#a8a29e;">
                            {{ $ignore }}
                        </td>
                    </tr>
                </table>
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">
                    <tr>
                        <td align="center" style="padding:16px 12px 0;font-size:12px;line-height:1.6;color:#a8a29e;">
                            {{ $appName }} · <a href="{{ $appUrl }}" style="color:#78716c;text-decoration:none;">{{ $appHost }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
