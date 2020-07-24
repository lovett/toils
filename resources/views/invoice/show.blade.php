<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            @font-face {
                font-family: 'Lato';
                font-style: italic;
                src: url({{ public_path('fonts/Lato-Italic.ttf') }}) format('truetype');
            }

            @font-face {
                font-family: 'Lato';
                font-weight: bold;
                font-style: italic;
                src: url({{ public_path('fonts/Lato-BlackItalic.ttf') }}) format('truetype');
            }


            @font-face {
                font-family: 'Lato';
                font-style: normal;
                font-weight: normal;
                src: url({{ public_path('fonts/Lato-Regular.ttf') }}) format('truetype');
            }

            @font-face {
                font-family: 'Lato';
                font-weight: bold;
                font-style: normal;
                src: url({{ public_path('fonts/Lato-Black.ttf') }}) format('truetype');
            }


            BODY {
                font-family: Lato;
                margin: 0 .5in;
            }

            .header {
                text-align: center;
            }

            H1 {
                font-weight: bolder;
                font-style: italic;
                font-size: 40pt;
            }

            H1 span {
                color: #FD5A00;
            }

            H2 {
                font-weight: bolder;
                font-style: italic;
                font-size: 28pt;
                margin-bottom: 1em;
            }

            .section {
                margin-bottom: 2em;
            }

            table {
                width: 100%;
            }

            td {
                vertical-align: middle;
            }

            td:first-of-type {
                width: 2.75in;
            }

            H3 {
                font-style: italic;
                font-size: 1em;
                margin: 0;
                padding: 0;
                line-height: 1;
            }

            address {
                font-style: normal;
                white-space: pre;
            }

            .due {
                text-transform: uppercase;
                font-size: 1.75em;
                font-weight: bolder;
            }

            .meta {
                border-color: #FD5A00;
                border-style: solid;
                border-width: 2pt 0;
                margin-bottom: 1em;
            }

            .meta hr {
                border: 0;
                border-bottom: 1pt solid #ccc;
            }

        </style>
    </head>
    <body>
        <div class="header">
            <h1><span>&lt;</span> {{ $user->displayName}} <span>&gt;</span></h1>
        </div>

        <h2>invoice</h2>
        <table cellspacing="0">
            <tr>
                <td>

                    <div class="section">
                        <h3>Submitted to:</h3>

                        <address>{{ AddressHelper::clientMailingAddress($client) }}</address>
                    </div>

                    <div class="section">
                        <h3>Please remit payment to:</h3>

                        <address>{{ AddressHelper::userMailingAddress($user) }}</address>
                    </div>
                </td>
                <td>
                    <div class="meta">
                        <p>Submitted: {{ TimeHelper::longDate($timezone, $invoice->sent) }}</p>
                        <hr/>
                        <p>Invoice number: {{ $invoice->number }}</p>
                    </div>

                    <div class="summary">
                        {{ $invoice->summary }}
                    </div>

                    <div class="due">
                        <p>AMOUNT DUE: {{ CurrencyHelper::money($invoice->amount) }}</p>

                        <p>DUE BY: {{ TimeHelper::longDate($timezone, $invoice->due) }}</p>
                    </div>
            </tr>
        </table>
    </body>
</html>
