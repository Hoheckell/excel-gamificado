<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: 'Montserrat', Arial, sans-serif; background-color: #f3f7f5; margin: 0; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 30px rgba(16,124,65,0.1);">
        <div style="background: #107c41; padding: 30px 25px; text-align: center;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="white" style="margin-bottom: 8px;">
                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zm8 8h-6v-2h6v2zm0-4h-6v-2h6v2zm0-4h-6V7h6v2z"/>
            </svg>
            <h1 style="color: #fff; font-size: 22px; margin: 0 0 5px;">Certificado Emitido!</h1>
            <p style="color: rgba(255,255,255,0.8); font-size: 14px; margin: 0;">Excel Workshop — Sistema Pedagógico Gamificado</p>
        </div>

        <div style="padding: 30px 25px;">
            <p style="color: #212529; font-size: 15px; line-height: 1.6; margin: 0 0 15px;">
                Olá <strong>{{ $nome_aluno }}</strong>,
            </p>
            <p style="color: #212529; font-size: 15px; line-height: 1.6; margin: 0 0 15px;">
                Seu <strong style="color: #107c41;">Certificado de Conquista</strong> foi emitido com sucesso! O arquivo PDF está anexado a este e-mail.
            </p>
            <p style="color: #212529; font-size: 15px; line-height: 1.6; margin: 0 0 15px;">
                Para verificar a autenticidade do certificado, utilize o código abaixo ou escaneie o QR Code presente no documento:
            </p>

            <div style="background: #e1f3e9; border-radius: 6px; padding: 15px; text-align: center; margin: 20px 0;">
                <span style="font-size: 18px; font-weight: 700; color: #107c41; font-family: 'Courier New', monospace; letter-spacing: 2px;">
                    {{ $codigo_validacao }}
                </span>
            </div>

            <a href="{{ route('certificados.validar', $codigo_validacao) }}"
               style="display: block; background: #107c41; color: #fff; text-align: center; padding: 14px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 15px; margin: 20px 0;">
                Validar Certificado Online
            </a>

            <p style="color: #666; font-size: 12px; line-height: 1.5; margin: 20px 0 0;">
                Este é um e-mail automático do Sistema Pedagógico Excel Workshop. Em caso de dúvidas, entre em contato com o professor responsável.
            </p>
        </div>

        <div style="background: #f3f3f3; padding: 15px 25px; text-align: center;">
            <p style="color: #999; font-size: 11px; margin: 0;">
                CENTEC &bull; Iguatu - CE &bull; 2026
            </p>
        </div>
    </div>
</body>
</html>
