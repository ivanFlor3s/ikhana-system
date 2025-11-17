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
                    <ul id="tocify-header-gestion-de-proveedores" class="tocify-header">
                <li class="tocify-item level-1" data-unique="gestion-de-proveedores">
                    <a href="#gestion-de-proveedores">Gestión de Proveedores</a>
                </li>
                                    <ul id="tocify-subheader-gestion-de-proveedores" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="gestion-de-proveedores-GETapi-providers">
                                <a href="#gestion-de-proveedores-GETapi-providers">Listar todos los proveedores</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="gestion-de-proveedores-POSTapi-providers">
                                <a href="#gestion-de-proveedores-POSTapi-providers">Crear un nuevo proveedor</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="gestion-de-proveedores-GETapi-providers--id-">
                                <a href="#gestion-de-proveedores-GETapi-providers--id-">Obtener un proveedor específico</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="gestion-de-proveedores-PUTapi-providers--id-">
                                <a href="#gestion-de-proveedores-PUTapi-providers--id-">Actualizar un proveedor</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="gestion-de-proveedores-DELETEapi-providers--id-">
                                <a href="#gestion-de-proveedores-DELETEapi-providers--id-">Eliminar un proveedor</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-opciones-del-sistema" class="tocify-header">
                <li class="tocify-item level-1" data-unique="opciones-del-sistema">
                    <a href="#opciones-del-sistema">Opciones del Sistema</a>
                </li>
                                    <ul id="tocify-subheader-opciones-del-sistema" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="opciones-del-sistema-GETapi-tax-statuses">
                                <a href="#opciones-del-sistema-GETapi-tax-statuses">Obtener opciones de posición frente al IVA</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="opciones-del-sistema-GETapi-agreements">
                                <a href="#opciones-del-sistema-GETapi-agreements">Obtener opciones de convenio</a>
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
        <li>Last updated: November 17, 2025</li>
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

                <h1 id="gestion-de-proveedores">Gestión de Proveedores</h1>

    <p>APIs para gestionar proveedores (suppliers/vendors)</p>

                                <h2 id="gestion-de-proveedores-GETapi-providers">Listar todos los proveedores</h2>

<p>
</p>

<p>Obtiene una lista de todos los proveedores del sistema.</p>

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
    &quot;message&quot;: &quot;Proveedores obtenidos exitosamente&quot;
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

                    <h2 id="gestion-de-proveedores-POSTapi-providers">Crear un nuevo proveedor</h2>

<p>
</p>

<p>Crea y almacena un nuevo proveedor en la base de datos.</p>

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
    &quot;message&quot;: &quot;Proveedor creado exitosamente&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, error de validación):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;cuit&quot;: [
            &quot;El campo CUIT es requerido.&quot;
        ],
        &quot;business_name&quot;: [
            &quot;El campo raz&oacute;n social es requerido.&quot;
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
<p>Razón social del proveedor. Example: <code>Proveedor Demo S.A.</code></p>
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
<p>optional Nombre de fantasía. Example: <code>Proveedor Demo</code></p>
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
<p>CUIT único (número de identificación tributaria). Example: <code>20-12345678-9</code></p>
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
<p>optional Ingresos Brutos. Example: <code>901-123456-7</code></p>
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
<p>optional Posición frente al IVA (usar GET /api/tax-statuses para obtener opciones). Example: <code>1</code></p>
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
<p>optional Convenio (usar GET /api/agreements para obtener opciones). Example: <code>convenio_multilateral</code></p>
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
<p>optional Teléfono principal. Example: <code>+54 11 1234-5678</code></p>
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
<p>optional Teléfono secundario. Example: <code>+54 11 8765-4321</code></p>
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
<p>optional Email principal. Example: <code>contacto@proveedor.com</code></p>
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
<p>optional Email secundario. Example: <code>ventas@proveedor.com</code></p>
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
<p>optional Dirección física. Example: <code>Av. Corrientes 1234, CABA</code></p>
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
<p>optional Sitio web. Example: <code>https://proveedor.com</code></p>
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
<p>optional Nombre de contacto. Example: <code>Juan Pérez</code></p>
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
<p>optional Observaciones adicionales. Example: <code>Cliente preferencial</code></p>
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
<p>optional Horario de apertura (HH:MM). Example: <code>09:00</code></p>
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
<p>optional Horario de cierre (HH:MM). Example: <code>18:00</code></p>
        </div>
        </form>

                    <h2 id="gestion-de-proveedores-GETapi-providers--id-">Obtener un proveedor específico</h2>

<p>
</p>

<p>Obtiene información detallada de un proveedor específico por su ID.</p>

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
    &quot;message&quot;: &quot;Proveedor obtenido exitosamente&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, no encontrado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Proveedor no encontrado&quot;
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
<p>ID del proveedor. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="gestion-de-proveedores-PUTapi-providers--id-">Actualizar un proveedor</h2>

<p>
</p>

<p>Actualiza la información de un proveedor existente. Solo envía los campos que deseas actualizar.</p>

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
    &quot;message&quot;: &quot;Proveedor actualizado exitosamente&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, no encontrado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Proveedor no encontrado&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, error de validación):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Error de validaci&oacute;n&quot;,
    &quot;errors&quot;: {
        &quot;email_2&quot;: [
            &quot;El email 2 debe ser una direcci&oacute;n de correo v&aacute;lida.&quot;
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
<p>ID del proveedor. Example: <code>1</code></p>
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
<p>optional Razón social. Example: <code>Proveedor Actualizado S.A.</code></p>
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
<p>optional Nombre de fantasía. Example: <code>Proveedor Actualizado</code></p>
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
<p>optional Teléfono secundario. Example: <code>+54 11 9999-8888</code></p>
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
<p>optional Email secundario. Example: <code>nuevo@proveedor.com</code></p>
        </div>
        </form>

                    <h2 id="gestion-de-proveedores-DELETEapi-providers--id-">Eliminar un proveedor</h2>

<p>
</p>

<p>Elimina un proveedor del sistema (soft delete). El proveedor será marcado como eliminado pero no se borrará permanentemente.</p>

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
    &quot;message&quot;: &quot;Proveedor eliminado exitosamente&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, no encontrado):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Proveedor no encontrado&quot;
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
<p>ID del proveedor. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="opciones-del-sistema">Opciones del Sistema</h1>

    <p>APIs para obtener enumeraciones y opciones para campos select</p>

                                <h2 id="opciones-del-sistema-GETapi-tax-statuses">Obtener opciones de posición frente al IVA</h2>

<p>
</p>

<p>Obtiene todas las opciones disponibles para el campo &quot;Posición frente al IVA&quot;.</p>

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
    &quot;message&quot;: &quot;Opciones de posici&oacute;n frente al IVA obtenidas exitosamente&quot;
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

                    <h2 id="opciones-del-sistema-GETapi-agreements">Obtener opciones de convenio</h2>

<p>
</p>

<p>Obtiene todas las opciones disponibles para el campo &quot;Convenio&quot;.</p>

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
    &quot;message&quot;: &quot;Opciones de convenio obtenidas exitosamente&quot;
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
