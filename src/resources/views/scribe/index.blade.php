<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Ikhana API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.5.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.5.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-GETapi-user">
                                <a href="#endpoints-GETapi-user">GET api/user</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-provider-management" class="tocify-header">
                <li class="tocify-item level-1" data-unique="provider-management">
                    <a href="#provider-management">Provider Management</a>
                </li>
                                    <ul id="tocify-subheader-provider-management" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="provider-management-GETapi-providers">
                                <a href="#provider-management-GETapi-providers">List all providers</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="provider-management-POSTapi-providers">
                                <a href="#provider-management-POSTapi-providers">Create a new provider</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="provider-management-GETapi-providers--id-">
                                <a href="#provider-management-GETapi-providers--id-">Get a single provider</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="provider-management-PUTapi-providers--id-">
                                <a href="#provider-management-PUTapi-providers--id-">Update a provider</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="provider-management-DELETEapi-providers--id-">
                                <a href="#provider-management-DELETEapi-providers--id-">Delete a provider</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-system-enums" class="tocify-header">
                <li class="tocify-item level-1" data-unique="system-enums">
                    <a href="#system-enums">System Enums</a>
                </li>
                                    <ul id="tocify-subheader-system-enums" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="system-enums-GETapi-tax-statuses">
                                <a href="#system-enums-GETapi-tax-statuses">Get tax status options</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="system-enums-GETapi-agreements">
                                <a href="#system-enums-GETapi-agreements">Get agreement options</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: November 16, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-GETapi-user">GET api/user</h2>

<p>
</p>



<span id="example-requests-GETapi-user">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/user" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/user"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-user">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-user" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-user"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-user"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-user">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-user" data-method="GET"
      data-path="api/user"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-user', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-user"
                    onclick="tryItOut('GETapi-user');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-user"
                    onclick="cancelTryOut('GETapi-user');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-user"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/user</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="provider-management">Provider Management</h1>

    <p>APIs for managing providers (suppliers/vendors)</p>

                                <h2 id="provider-management-GETapi-providers">List all providers</h2>

<p>
</p>

<p>Get a paginated list of all providers in the system.</p>

<span id="example-requests-GETapi-providers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/providers" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/providers"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-providers">
            <blockquote>
            <p>Example response (200, success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;fantasy_name&quot;: &quot;Proveedor Demo&quot;,
            &quot;business_name&quot;: &quot;Proveedor Demo S.A.&quot;,
            &quot;cuit&quot;: &quot;20-12345678-9&quot;,
            &quot;iibb&quot;: &quot;901-123456-7&quot;,
            &quot;tax_status&quot;: &quot;Responsable Inscripto&quot;,
            &quot;agreement&quot;: &quot;Convenio Multilateral&quot;,
            &quot;phone_1&quot;: &quot;+54 11 1234-5678&quot;,
            &quot;phone_2&quot;: null,
            &quot;email_1&quot;: &quot;contacto@proveedor.com&quot;,
            &quot;address&quot;: &quot;Av. Corrientes 1234, CABA&quot;,
            &quot;website&quot;: &quot;https://proveedor.com&quot;,
            &quot;contact_name&quot;: &quot;Juan P&eacute;rez&quot;,
            &quot;observations&quot;: &quot;Cliente preferencial&quot;,
            &quot;business_hours_start&quot;: &quot;09:00&quot;,
            &quot;business_hours_end&quot;: &quot;18:00&quot;,
            &quot;created_at&quot;: &quot;2024-11-16T10:00:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2024-11-16T10:00:00.000000Z&quot;,
            &quot;deleted_at&quot;: null
        }
    ],
    &quot;message&quot;: &quot;Providers retrieved successfully&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-providers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-providers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-providers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-providers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-providers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-providers" data-method="GET"
      data-path="api/providers"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-providers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-providers"
                    onclick="tryItOut('GETapi-providers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-providers"
                    onclick="cancelTryOut('GETapi-providers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-providers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/providers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-providers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-providers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="provider-management-POSTapi-providers">Create a new provider</h2>

<p>
</p>

<p>Store a newly created provider in the database.</p>

<span id="example-requests-POSTapi-providers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/providers" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"business_name\": \"Proveedor Demo S.A.\",
    \"fantasy_name\": \"Proveedor Demo\",
    \"cuit\": \"20-12345678-9\",
    \"iibb\": \"901-123456-7\",
    \"tax_status\": \"1\",
    \"agreement\": \"convenio_multilateral\",
    \"phone_1\": \"+54 11 1234-5678\",
    \"phone_2\": \"+54 11 8765-4321\",
    \"email_1\": \"contacto@proveedor.com\",
    \"email_2\": \"ventas@proveedor.com\",
    \"address\": \"Av. Corrientes 1234, CABA\",
    \"website\": \"https:\\/\\/proveedor.com\",
    \"contact_name\": \"Juan Pérez\",
    \"observations\": \"Cliente preferencial\",
    \"business_hours_start\": \"09:00\",
    \"business_hours_end\": \"18:00\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/providers"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "business_name": "Proveedor Demo S.A.",
    "fantasy_name": "Proveedor Demo",
    "cuit": "20-12345678-9",
    "iibb": "901-123456-7",
    "tax_status": "1",
    "agreement": "convenio_multilateral",
    "phone_1": "+54 11 1234-5678",
    "phone_2": "+54 11 8765-4321",
    "email_1": "contacto@proveedor.com",
    "email_2": "ventas@proveedor.com",
    "address": "Av. Corrientes 1234, CABA",
    "website": "https:\/\/proveedor.com",
    "contact_name": "Juan Pérez",
    "observations": "Cliente preferencial",
    "business_hours_start": "09:00",
    "business_hours_end": "18:00"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-providers">
            <blockquote>
            <p>Example response (201, success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;business_name&quot;: &quot;Proveedor Demo S.A.&quot;,
        &quot;fantasy_name&quot;: &quot;Proveedor Demo&quot;,
        &quot;cuit&quot;: &quot;20-12345678-9&quot;,
        &quot;created_at&quot;: &quot;2024-11-16T10:00:00.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2024-11-16T10:00:00.000000Z&quot;
    },
    &quot;message&quot;: &quot;Provider created successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, validation error):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Validation error&quot;,
    &quot;errors&quot;: {
        &quot;cuit&quot;: [
            &quot;The cuit field is required.&quot;
        ],
        &quot;business_name&quot;: [
            &quot;The business name field is required.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-providers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-providers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-providers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-providers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-providers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-providers" data-method="POST"
      data-path="api/providers"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-providers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-providers"
                    onclick="tryItOut('POSTapi-providers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-providers"
                    onclick="cancelTryOut('POSTapi-providers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-providers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/providers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-providers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-providers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>business_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="business_name"                data-endpoint="POSTapi-providers"
               value="Proveedor Demo S.A."
               data-component="body">
    <br>
<p>The legal business name. Example: <code>Proveedor Demo S.A.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fantasy_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fantasy_name"                data-endpoint="POSTapi-providers"
               value="Proveedor Demo"
               data-component="body">
    <br>
<p>optional The fantasy/trade name. Example: <code>Proveedor Demo</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>cuit</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="cuit"                data-endpoint="POSTapi-providers"
               value="20-12345678-9"
               data-component="body">
    <br>
<p>Unique tax identification number (CUIT). Example: <code>20-12345678-9</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>iibb</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="iibb"                data-endpoint="POSTapi-providers"
               value="901-123456-7"
               data-component="body">
    <br>
<p>optional Provincial tax identification. Example: <code>901-123456-7</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>tax_status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tax_status"                data-endpoint="POSTapi-providers"
               value="1"
               data-component="body">
    <br>
<p>optional Tax status value (use GET /api/tax-statuses to get options). Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>agreement</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="agreement"                data-endpoint="POSTapi-providers"
               value="convenio_multilateral"
               data-component="body">
    <br>
<p>optional Agreement value (use GET /api/agreements to get options). Example: <code>convenio_multilateral</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone_1</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone_1"                data-endpoint="POSTapi-providers"
               value="+54 11 1234-5678"
               data-component="body">
    <br>
<p>optional Primary phone number. Example: <code>+54 11 1234-5678</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone_2</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone_2"                data-endpoint="POSTapi-providers"
               value="+54 11 8765-4321"
               data-component="body">
    <br>
<p>optional Secondary phone number. Example: <code>+54 11 8765-4321</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email_1</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email_1"                data-endpoint="POSTapi-providers"
               value="contacto@proveedor.com"
               data-component="body">
    <br>
<p>optional Primary email address. Example: <code>contacto@proveedor.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email_2</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email_2"                data-endpoint="POSTapi-providers"
               value="ventas@proveedor.com"
               data-component="body">
    <br>
<p>optional Secondary email address. Example: <code>ventas@proveedor.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>address</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="address"                data-endpoint="POSTapi-providers"
               value="Av. Corrientes 1234, CABA"
               data-component="body">
    <br>
<p>optional Physical address. Example: <code>Av. Corrientes 1234, CABA</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>website</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="website"                data-endpoint="POSTapi-providers"
               value="https://proveedor.com"
               data-component="body">
    <br>
<p>optional Website URL. Example: <code>https://proveedor.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>contact_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="contact_name"                data-endpoint="POSTapi-providers"
               value="Juan Pérez"
               data-component="body">
    <br>
<p>optional Contact person name. Example: <code>Juan Pérez</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>observations</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="observations"                data-endpoint="POSTapi-providers"
               value="Cliente preferencial"
               data-component="body">
    <br>
<p>optional Additional notes. Example: <code>Cliente preferencial</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>business_hours_start</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="business_hours_start"                data-endpoint="POSTapi-providers"
               value="09:00"
               data-component="body">
    <br>
<p>optional Opening time (HH:MM). Example: <code>09:00</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>business_hours_end</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="business_hours_end"                data-endpoint="POSTapi-providers"
               value="18:00"
               data-component="body">
    <br>
<p>optional Closing time (HH:MM). Example: <code>18:00</code></p>
        </div>
        </form>

                    <h2 id="provider-management-GETapi-providers--id-">Get a single provider</h2>

<p>
</p>

<p>Retrieve detailed information about a specific provider by ID.</p>

<span id="example-requests-GETapi-providers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/providers/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/providers/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-providers--id-">
            <blockquote>
            <p>Example response (200, success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;fantasy_name&quot;: &quot;Proveedor Demo&quot;,
        &quot;business_name&quot;: &quot;Proveedor Demo S.A.&quot;,
        &quot;cuit&quot;: &quot;20-12345678-9&quot;,
        &quot;iibb&quot;: &quot;901-123456-7&quot;,
        &quot;tax_status&quot;: &quot;Responsable Inscripto&quot;,
        &quot;agreement&quot;: &quot;Convenio Multilateral&quot;,
        &quot;phone_1&quot;: &quot;+54 11 1234-5678&quot;,
        &quot;email_1&quot;: &quot;contacto@proveedor.com&quot;,
        &quot;address&quot;: &quot;Av. Corrientes 1234, CABA&quot;,
        &quot;website&quot;: &quot;https://proveedor.com&quot;,
        &quot;contact_name&quot;: &quot;Juan P&eacute;rez&quot;,
        &quot;observations&quot;: &quot;Cliente preferencial&quot;,
        &quot;business_hours_start&quot;: &quot;09:00&quot;,
        &quot;business_hours_end&quot;: &quot;18:00&quot;,
        &quot;created_at&quot;: &quot;2024-11-16T10:00:00.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2024-11-16T10:00:00.000000Z&quot;
    },
    &quot;message&quot;: &quot;Provider retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, not found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Provider not found&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-providers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-providers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-providers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-providers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-providers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-providers--id-" data-method="GET"
      data-path="api/providers/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-providers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-providers--id-"
                    onclick="tryItOut('GETapi-providers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-providers--id-"
                    onclick="cancelTryOut('GETapi-providers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-providers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/providers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-providers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-providers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-providers--id-"
               value="1"
               data-component="url">
    <br>
<p>The provider ID. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="provider-management-PUTapi-providers--id-">Update a provider</h2>

<p>
</p>

<p>Update an existing provider's information. Only send the fields you want to update.</p>

<span id="example-requests-PUTapi-providers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost:8000/api/providers/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"business_name\": \"Proveedor Actualizado S.A.\",
    \"fantasy_name\": \"Proveedor Actualizado\",
    \"phone_2\": \"+54 11 9999-8888\",
    \"email_2\": \"nuevo@proveedor.com\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/providers/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "business_name": "Proveedor Actualizado S.A.",
    "fantasy_name": "Proveedor Actualizado",
    "phone_2": "+54 11 9999-8888",
    "email_2": "nuevo@proveedor.com"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-providers--id-">
            <blockquote>
            <p>Example response (200, success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;fantasy_name&quot;: &quot;Proveedor Actualizado&quot;,
        &quot;business_name&quot;: &quot;Proveedor Actualizado S.A.&quot;,
        &quot;cuit&quot;: &quot;20-12345678-9&quot;,
        &quot;updated_at&quot;: &quot;2024-11-16T11:00:00.000000Z&quot;
    },
    &quot;message&quot;: &quot;Provider updated successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, not found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Provider not found&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, validation error):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Validation error&quot;,
    &quot;errors&quot;: {
        &quot;email_2&quot;: [
            &quot;The email 2 must be a valid email address.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-providers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-providers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-providers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-providers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-providers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-providers--id-" data-method="PUT"
      data-path="api/providers/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-providers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-providers--id-"
                    onclick="tryItOut('PUTapi-providers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-providers--id-"
                    onclick="cancelTryOut('PUTapi-providers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-providers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/providers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-providers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-providers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-providers--id-"
               value="1"
               data-component="url">
    <br>
<p>The provider ID. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>business_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="business_name"                data-endpoint="PUTapi-providers--id-"
               value="Proveedor Actualizado S.A."
               data-component="body">
    <br>
<p>optional The legal business name. Example: <code>Proveedor Actualizado S.A.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>fantasy_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fantasy_name"                data-endpoint="PUTapi-providers--id-"
               value="Proveedor Actualizado"
               data-component="body">
    <br>
<p>optional The fantasy/trade name. Example: <code>Proveedor Actualizado</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone_2</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone_2"                data-endpoint="PUTapi-providers--id-"
               value="+54 11 9999-8888"
               data-component="body">
    <br>
<p>optional Secondary phone number. Example: <code>+54 11 9999-8888</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email_2</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email_2"                data-endpoint="PUTapi-providers--id-"
               value="nuevo@proveedor.com"
               data-component="body">
    <br>
<p>optional Secondary email address. Example: <code>nuevo@proveedor.com</code></p>
        </div>
        </form>

                    <h2 id="provider-management-DELETEapi-providers--id-">Delete a provider</h2>

<p>
</p>

<p>Soft delete a provider from the system. The provider will be marked as deleted but not permanently removed.</p>

<span id="example-requests-DELETEapi-providers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost:8000/api/providers/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/providers/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-providers--id-">
            <blockquote>
            <p>Example response (200, success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;message&quot;: &quot;Provider deleted successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, not found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Provider not found&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-providers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-providers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-providers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-providers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-providers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-providers--id-" data-method="DELETE"
      data-path="api/providers/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-providers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-providers--id-"
                    onclick="tryItOut('DELETEapi-providers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-providers--id-"
                    onclick="cancelTryOut('DELETEapi-providers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-providers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/providers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-providers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-providers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="DELETEapi-providers--id-"
               value="1"
               data-component="url">
    <br>
<p>The provider ID. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="system-enums">System Enums</h1>

    <p>APIs for getting system enumerations and select options</p>

                                <h2 id="system-enums-GETapi-tax-statuses">Get tax status options</h2>

<p>
</p>

<p>Get all available tax status options for the &quot;Posición frente al IVA&quot; field.</p>

<span id="example-requests-GETapi-tax-statuses">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/tax-statuses" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/tax-statuses"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-tax-statuses">
            <blockquote>
            <p>Example response (200, success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;value&quot;: &quot;1&quot;,
            &quot;label&quot;: &quot;IVA Responsable Inscripto&quot;
        },
        {
            &quot;value&quot;: &quot;2&quot;,
            &quot;label&quot;: &quot;IVA Responsable no Inscripto&quot;
        },
        {
            &quot;value&quot;: &quot;3&quot;,
            &quot;label&quot;: &quot;IVA no Responsable&quot;
        }
    ],
    &quot;message&quot;: &quot;Tax status options retrieved successfully&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-tax-statuses" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-tax-statuses"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-tax-statuses"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-tax-statuses" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-tax-statuses">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-tax-statuses" data-method="GET"
      data-path="api/tax-statuses"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-tax-statuses', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-tax-statuses"
                    onclick="tryItOut('GETapi-tax-statuses');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-tax-statuses"
                    onclick="cancelTryOut('GETapi-tax-statuses');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-tax-statuses"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/tax-statuses</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-tax-statuses"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-tax-statuses"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="system-enums-GETapi-agreements">Get agreement options</h2>

<p>
</p>

<p>Get all available agreement options for the &quot;Convenio&quot; field.</p>

<span id="example-requests-GETapi-agreements">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/agreements" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/agreements"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-agreements">
            <blockquote>
            <p>Example response (200, success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;value&quot;: &quot;convenio_multilateral&quot;,
            &quot;label&quot;: &quot;Convenio Multilateral&quot;
        }
    ],
    &quot;message&quot;: &quot;Agreement options retrieved successfully&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-agreements" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-agreements"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-agreements"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-agreements" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-agreements">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-agreements" data-method="GET"
      data-path="api/agreements"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-agreements', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-agreements"
                    onclick="tryItOut('GETapi-agreements');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-agreements"
                    onclick="cancelTryOut('GETapi-agreements');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-agreements"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/agreements</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-agreements"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-agreements"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
