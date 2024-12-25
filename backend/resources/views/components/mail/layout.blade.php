<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no, url=no">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings xmlns:o="urn:schemas-microsoft-com:office:office">
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <style>
        td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family: "Segoe UI", sans-serif; mso-line-height-rule: exactly;}
    </style>
    <![endif]-->
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        /* Base */
        :root {
            color-scheme: light dark;
            supported-color-schemes: light dark;
        }
        
        html {
            font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #f3f4f6;
        }

        /* Layout */
        .email-wrapper {
            background-color: #f3f4f6;
            padding: 24px;
        }

        .email-content {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .email-header {
            padding: 24px;
            background-color: {{ config('app.primary_color', '#4f46e5') }};
        }

        .email-body {
            padding: 32px 24px;
            background-color: #ffffff;
            color: #111827;
        }

        .email-footer {
            padding: 24px;
            background-color: #f9fafb;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }

        /* Typography */
        .text-lg {
            font-size: 18px;
            line-height: 28px;
        }

        .text-base {
            font-size: 16px;
            line-height: 24px;
        }

        .text-sm {
            font-size: 14px;
            line-height: 20px;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-gray {
            color: #6b7280;
        }

        .text-white {
            color: #ffffff;
        }

        /* Links & Buttons */
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: {{ config('app.primary_color', '#4f46e5') }};
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
        }

        .link {
            color: {{ config('app.primary_color', '#4f46e5') }};
            text-decoration: none;
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1f2937;
            }
            
            .email-wrapper {
                background-color: #1f2937;
            }
            
            .email-content {
                background-color: #111827;
                border: 1px solid #374151;
            }
            
            .email-body {
                background-color: #111827;
                color: #f9fafb;
            }
            
            .email-footer {
                background-color: #1f2937;
                color: #9ca3af;
                border-top: 1px solid #374151;
            }

            .text-gray {
                color: #9ca3af;
            }

            .button {
                background-color: {{ config('app.primary_color', '#4f46e5') }};
                color: #ffffff !important;
            }

            .link {
                color: {{ config('app.primary_color', '#4f46e5') }};
            }

            /* Fix for dark mode text colors */
            .email-body p, 
            .email-body strong, 
            .email-body li,
            .email-body td {
                color: #f9fafb;
            }

            /* Background for info boxes in dark mode */
            .info-box {
                background-color: #1f2937 !important;
                border: 1px solid #374151;
            }
        }

        /* Mobile Responsiveness */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 12px;
            }

            .email-content {
                border-radius: 4px;
            }

            .email-header, .email-body, .email-footer {
                padding: 20px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <!-- Header -->
            <div class="email-header">
                <img src="{{ asset('images/logo-white.png') }}" alt="{{ config('app.name') }}" style="height: 40px;">
            </div>

            <!-- Body -->
            <div class="email-body">
                @if(isset($greeting))
                    <p class="text-lg font-bold">{{ $greeting }}</p>
                @endif

                {{ $slot }}

                @if(isset($action))
                    <table style="width: 100%; text-align: center; margin: 32px 0;">
                        <tr>
                            <td>
                                <a href="{{ $action['url'] }}" class="button" target="_blank">
                                    {{ $action['text'] }}
                                </a>
                            </td>
                        </tr>
                    </table>
                @endif

                @if(isset($closing))
                    <p class="text-base">{{ $closing }}</p>
                @endif

                <p class="text-base">
                    Regards,<br>
                    {{ config('app.name') }} Team
                </p>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p class="text-sm">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
                @if(isset($unsubscribe))
                    <p class="text-sm">
                        <a href="{{ $unsubscribe }}" class="link">Unsubscribe</a>
                    </p>
                @endif
                <p class="text-sm">
                    {!! isset($address) ? $address : config('app.address', 'Your Company Address') !!}
                </p>
            </div>
        </div>
    </div>
</body>
</html> 