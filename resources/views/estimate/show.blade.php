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

            @page {
                margin: 1in;
            }

            body {
                font-family: Lato;
            }

            .header {
                text-align: center;
            }

            .header h1 {
                font-weight: bolder;
                font-style: italic;
                font-size: 40pt;
                margin-top: 0;
            }

            .header h1 span {
                color: #FD5A00;
            }

            .header h2 {
                font-weight: bolder;
                font-style: italic;
                font-size: 24pt;
                line-height: 1;
                margin-bottom: 0;
                text-align: center;
            }

            .header .date {
                margin-bottom: 1em;
            }

            .main h1 {
                font-style: italic;
                font-size: 1.3em;
                margin: 0;
                padding: 0;
                line-height: 1;
                page-break-after: avoid;
            }

            .main h1 + p {
                margin-top: .5em;
            }

            .main p + h1 {
                margin-top: 1.5em;
            }

            .main li {
                padding-bottom: .5em;
            }

            .main li p {
                margin: 0;
                padding: 0;
                line-height: 1;
            }

            #footer {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
            }

            #footer .page-number {
                text-align: right;
                font-size: .85em;
            }

            #footer .page-number:before {
                content: counter(page);
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1><span>&lt;</span> {{ $user->displayName }} <span>&gt;</span></h1>

            <h2>
                {{ $name }}
                <br/>
                statement of work
            </h2>

            <p class="date">{{ TimeHelper::longDate($timezone, $date) }}</p>
        </div>

        <div class="main">
            {!! $statementOfWork !!}
        </div>

        <div id="footer">
            <div class="page-number"></div>
        </div>

    </body>
</html>
