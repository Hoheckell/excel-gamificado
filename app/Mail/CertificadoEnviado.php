<?php

namespace App\Mail;

use App\Models\Certificado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificadoEnviado extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Certificado $certificado,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seu Certificado de Conquista — Excel Workshop',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificado',
            with: [
                'nome_aluno' => $this->certificado->nome_aluno,
                'codigo_validacao' => $this->certificado->codigo_validacao,
            ],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.certificado', ['certificado' => $this->certificado])->setPaper('a4', 'landscape') // Força o A4 Paisagem no PHP
          ->setOption('isRemoteEnabled', true) // <-- PERMITE CARREGAR O QR CODE DA INTERNET!
          ->setOption('margin_top', 0)
          ->setOption('margin_bottom', 0)
          ->setOption('margin_left', 0)
          ->setOption('margin_right', 0);

        $filename = 'Certificado_' . preg_replace('/[^a-zA-Z0-9]/', '_', $this->certificado->nome_aluno) . '.pdf';

        return [
            Attachment::fromData(fn() => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
