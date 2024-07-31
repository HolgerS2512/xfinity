<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title></title>
  <style>
    *{font-family: 'Open sans', Arial, Helvetica, sans-serif}
    table {
      font-family: arial, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }
    
    td, th {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
    }
    
    tr:nth-child(even) {
      background-color: #dddddd;
    }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400&amp;family=Fira+Sans:wght@400&amp;display=swap" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <h1>System-E-Mail: Kontaktanfrage</h1>
  
  
    <table>
      <tr>
        <th>Attribut</th>
        <th>Daten</th>
      </tr>
      <tr>
        <td>Anrede</td>
        <td>{{ $salutation }}</td>
      </tr>
      <tr>
        <td>Vorname</td>
        <td>{{ $firstname }}</td>
      </tr>
      <tr>
        <td>Nachname</td>
        <td>{{ $lastname }}</td>
      </tr>
      <tr>
        <td>E-Mail</td>
        <td>{{ $email }}</td>
      </tr>
      <tr>
        <td>Telefonnummer</td>
        <td>{{ $phone }}</td>
      </tr>
    </table>

    <br>
    <br>

    <h4>Nutzernachricht:</h4>
    <p style="margin: 4px; max-width: 600px; line-height: 1.5;">{!! $msg !!}</p>

    <br>
    <br>
  
    <p>
      <a href="mailto:{{ $email }}">
        <button style="border: 1px solid black; border-radius:80px; padding: 12px 24px; color: white;background:black; cursor: pointer; font-size:20px;">Neue Mail öffnen</button>
      </a>
    </p>
  
    <br>
    <br>
    
    <img src="{{ $logo }}" width="60" height="auto">
  </body>
  </html>