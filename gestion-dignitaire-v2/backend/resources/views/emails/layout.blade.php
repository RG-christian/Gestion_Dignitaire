<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>@yield('subject', 'Gestion Dignitaires')</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:12px; overflow:hidden;">
          <tr>
            <td style="background: linear-gradient(90deg, #16a34a 0%, #eab308 50%, #2563eb 100%); padding:20px 32px;">
              <span style="color:#ffffff; font-size:18px; font-weight:bold;">Gestion Dignitaires</span>
              <div style="color:#ffffff; font-size:12px; opacity:.9;">République Gabonaise</div>
            </td>
          </tr>
          <tr>
            <td style="padding:32px; color:#1f2937; font-size:14px; line-height:1.6;">
              @yield('content')
            </td>
          </tr>
          <tr>
            <td style="padding:20px 32px; background-color:#f9fafb; color:#6b7280; font-size:12px;">
              Cet email a été envoyé automatiquement, merci de ne pas y répondre.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
