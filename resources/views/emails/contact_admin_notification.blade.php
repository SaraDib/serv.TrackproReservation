<html>
  <body>
    <h2>Nouvelle demande de contact reçue</h2>
    <p>
      <strong>Nom:</strong> {{ $submission->name }}<br>
      <strong>Email:</strong> {{ $submission->email }}<br>
      <strong>Téléphone:</strong> {{ $submission->phone ?? 'Non fourni' }}<br>
      <strong>Entreprise:</strong> {{ $submission->company ?? 'Non fourni' }}<br>
      <strong>Sujet:</strong> {{ $submission->subject }}
    </p>
    <p>
      <strong>Message:</strong><br>
      {!! nl2br(e($submission->message)) !!}
    </p>
    <p>
      Reçu le {{ $submission->created_at->format('d/m/Y H:i') }}
    </p>
  </body>
</html>