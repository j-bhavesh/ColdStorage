<x-admin-layout>
    @section('title', 'SMS Test')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">SMS Test</h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">SMS Test</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>

    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Test Single SMS</h3>
                        </div>
                        <div class="card-body">
                            <form id="singleSmsForm">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="9876543210" required>
                                </div>
                                <div class="mb-3">
                                    <label for="template_id" class="form-label">Select Template</label>
                                    <select class="form-select" id="template_id" name="template_id" required>
                                        <option value="">Select a template</option>
                                        @if($templates['success'] && !empty($templates['templates']))
                                            @foreach($templates['templates'] as $template)
                                                <option value="{{ $template['TemplateId'] }}">
                                                    {{ $template['Template'] ?? $template['Content'] ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="variable_0" class="form-label">Variable 1</label>
                                    <input type="text" class="form-control" id="variable_0" name="variable_0">
                                </div>
                                <div class="mb-3">
                                    <label for="variable_1" class="form-label">Variable 2</label>
                                    <input type="text" class="form-control" id="variable_1" name="variable_1">
                                </div>
                                <div class="mb-3">
                                    <label for="variable_2" class="form-label">Variable 3</label>
                                    <input type="text" class="form-control" id="variable_2" name="variable_2">
                                </div>
                                <div class="mb-3">
                                    <label for="variable_3" class="form-label">Variable 4</label>
                                    <input type="text" class="form-control" id="variable_3" name="variable_3">
                                </div>
                                <div class="mb-3">
                                    <label for="variable_4" class="form-label">Variable 5</label>
                                    <input type="text" class="form-control" id="variable_4" name="variable_4">
                                </div>
                                <div class="mb-3">
                                    <label for="variable_5" class="form-label">Variable 6</label>
                                    <input type="text" class="form-control" id="variable_5" name="variable_5">
                                </div>
                                <div class="mb-3">
                                    <label for="variable_6" class="form-label">Variable 7</label>
                                    <input type="text" class="form-control" id="variable_6" name="variable_6">
                                </div>
                                <div class="mb-3">
                                    <label for="variable_7" class="form-label">Variable 8</label>
                                    <input type="text" class="form-control" id="variable_7" name="variable_7">
                                </div>
                                <button type="submit" class="btn btn-primary">Send SMS</button>
                            </form>
                            <div id="singleSmsResult" class="mt-3"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card d-none">
                        <div class="card-header">
                            <h3 class="card-title">Test Bulk SMS</h3>
                        </div>
                        <div class="card-body">
                            <form id="bulkSmsForm">
                                <div class="mb-3">
                                    <label for="phones" class="form-label">Phone Numbers (one per line)</label>
                                    <textarea class="form-control" id="phones" name="phones" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="bulkTemplateId" class="form-label">Template ID</label>
                                    <input type="text" class="form-control" id="bulkTemplateId" name="bulkTemplateId" required>
                                </div>
                                <div class="mb-3">
                                    <label for="bulk_variable_0" class="form-label">Variable 1</label>
                                    <input type="text" class="form-control" id="bulk_variable_0" name="bulk_variable_0">
                                </div>
                                <div class="mb-3">
                                    <label for="bulk_variable_1" class="form-label">Variable 2</label>
                                    <input type="text" class="form-control" id="bulk_variable_1" name="bulk_variable_1">
                                </div>
                                <div class="mb-3">
                                    <label for="bulk_variable_2" class="form-label">Variable 3</label>
                                    <input type="text" class="form-control" id="bulk_variable_2" name="bulk_variable_2">
                                </div>
                                <button type="submit" class="btn btn-primary">Send Bulk SMS</button>
                            </form>
                            <div id="bulkSmsResult" class="mt-3"></div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Available Sender IDs</h3>
                        </div>
                        <div class="card-body">
                            <button id="getSenderIds" class="btn btn-info">Get Sender IDs</button>
                            <div id="senderIdsResult" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>

            

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">SMS Templates</h5>
                    <button type="button" class="btn btn-primary" onclick="getTemplates()">
                        Get Templates
                    </button>
                </div>
                <div class="card-body">
                    <div id="templatesList">
                        <!-- Templates will be displayed here -->
                    </div>
                </div>
            </div>

        </div>
        <!--end::Container-->
    </div>

    @push('scripts')
    <script>
        // Get CSRF token from meta tag or cookie
        function getCsrfToken() {
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                return metaTag.content;
            }
            // Fallback to getting from cookie
            const name = 'XSRF-TOKEN=';
            const decodedCookie = decodeURIComponent(document.cookie);
            const cookieArray = decodedCookie.split(';');
            for (let cookie of cookieArray) {
                cookie = cookie.trim();
                if (cookie.indexOf(name) === 0) {
                    return cookie.substring(name.length, cookie.length);
                }
            }
            return '';
        }

        // Single SMS Form
        document.getElementById('singleSmsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const resultDiv = document.getElementById('singleSmsResult');
            resultDiv.innerHTML = '<div class="alert alert-info">Sending SMS...</div>';
            
            const phone = document.getElementById('phone').value;
            const templateId = document.getElementById('template_id').value;
            
            // Get all variable inputs
            const variables = [];
            for (let i = 0; i <= 7; i++) {
                const variableInput = document.getElementById(`variable_${i}`);
                if (variableInput && variableInput.value.trim()) {
                    variables.push(variableInput.value.trim());
                }
            }

            try {
                const response = await fetch('/administrator/sms/test/single', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        phone: phone,
                        template_id: templateId,
                        variables: variables
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    let successHtml = `
                        <div class="alert alert-success">
                            <h5>✅ SMS Sent Successfully</h5>
                            <p><strong>Message:</strong> ${data.message}</p>
                            <p><strong>API ID:</strong> ${data.raw_response.ApiId}</p>
                            <p><strong>Message UUID:</strong> ${data.raw_response.MessageUUID}</p>
                            <p><strong>Status:</strong> ${data.raw_response.Message}</p>
                        </div>
                    `;
                    resultDiv.innerHTML = successHtml;
                    document.getElementById('singleSmsForm').reset();
                } else {
                    let errorHtml = `
                        <div class="alert alert-danger">
                            <h5>❌ Failed to Send SMS</h5>
                            <p><strong>Error:</strong> ${data.message}</p>
                            ${data.error_code ? `<p><strong>Error Code:</strong> ${data.error_code}</p>` : ''}
                        </div>
                    `;
                    resultDiv.innerHTML = errorHtml;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>❌ Error</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        });

        // Bulk SMS Form
        document.getElementById('bulkSmsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const resultDiv = document.getElementById('bulkSmsResult');
            resultDiv.innerHTML = '<div class="alert alert-info">Sending Bulk SMS...</div>';
            
            const phones = document.getElementById('phones').value.split('\n').map(p => p.trim()).filter(p => p);
            const templateId = document.getElementById('bulkTemplateId').value;
            
            // Get all variable inputs
            const variables = [];
            for (let i = 0; i <= 2; i++) {
                const variableInput = document.getElementById(`bulk_variable_${i}`);
                if (variableInput && variableInput.value.trim()) {
                    variables.push(variableInput.value.trim());
                }
            }

            try {
                const response = await fetch('/administrator/sms/test/bulk', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        phones: phones,
                        template_id: templateId,
                        variables: variables
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    let successHtml = `
                        <div class="alert alert-success">
                            <h5>✅ Bulk SMS Sent Successfully</h5>
                            <p><strong>Message:</strong> ${data.message}</p>
                            <p><strong>API ID:</strong> ${data.raw_response.ApiId}</p>
                            <p><strong>Message UUID:</strong> ${data.raw_response.MessageUUID}</p>
                            <p><strong>Status:</strong> ${data.raw_response.Message}</p>
                        </div>
                    `;
                    resultDiv.innerHTML = successHtml;
                    document.getElementById('bulkSmsForm').reset();
                } else {
                    let errorHtml = `
                        <div class="alert alert-danger">
                            <h5>❌ Failed to Send Bulk SMS</h5>
                            <p><strong>Error:</strong> ${data.message}</p>
                            ${data.error_code ? `<p><strong>Error Code:</strong> ${data.error_code}</p>` : ''}
                        </div>
                    `;
                    resultDiv.innerHTML = errorHtml;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>❌ Error</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        });

        // Get Sender IDs
        document.getElementById('getSenderIds').addEventListener('click', async () => {
            const resultDiv = document.getElementById('senderIdsResult');
            resultDiv.innerHTML = '<div class="alert alert-info">Fetching Sender IDs...</div>';

            try {
                const response = await fetch('/administrator/sms/sender-ids', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                
                if (result.success) {
                    const senderIds = result.sender_ids;
                    if (senderIds && senderIds.length > 0) {
                        resultDiv.innerHTML = `
                            <div class="alert alert-success">
                                <h5>Available Sender IDs:</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sender ID</th>
                                            <th>Expiry Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${senderIds.map(id => `
                                            <tr>
                                                <td><strong>${id.id}</strong></td>
                                                <td>${new Date(id.expiry_date).toLocaleString()}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else {
                        resultDiv.innerHTML = `
                            <div class="alert alert-warning">
                                No Sender IDs found. Please create a Sender ID in your SMS Country account.
                            </div>
                        `;
                    }
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <h5>❌ Error</h5>
                            <p>${result.message || 'Failed to get Sender IDs'}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>❌ Error</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        });

        // Get Templates
        async function getTemplates() {
            const resultDiv = document.getElementById('templatesList');
            resultDiv.innerHTML = '<div class="alert alert-info">Fetching SMS Templates...</div>';

            try {
                const response = await fetch('/administrator/sms/templates', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    const templates = data.templates;
                    if (templates && templates.length > 0) {
                        resultDiv.innerHTML = `
                            <div class="alert alert-success">
                                <h5>Available Templates:</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Template No.</th>
                                            <th>Template ID</th>
                                            <th>Template Text</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${templates.map((template, index) => `
                                            <tr>
                                                <td><strong>${index + 1}</strong></td>
                                                <td><strong>${template.TemplateId}</strong></td>
                                                <td>${template.Template || template.Content || 'N/A'}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else {
                        resultDiv.innerHTML = '<div class="alert alert-warning">No templates found</div>';
                    }
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <h5>❌ Error</h5>
                            <p>${data.message || 'Failed to get templates'}</p>
                        </div>
                    `;
                }
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>❌ Error</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            }
        }
    </script>
    @endpush
</x-admin-layout> 