/**
 * Settings Page Functions
 * JIMS - Jewellery Inventory Management System
 */

// Settings Functions
async function loadSettings() {
    try {
        showLoading();
        
        // Get current system settings
        const settingsData = await getSystemSettings();
        
        renderSettings(settingsData);
    } catch (error) {
        console.error('Failed to load settings:', error);
        renderSettings({});
    } finally {
        hideLoading();
    }
}

async function getSystemSettings() {
    // This would typically fetch from API
    // For now, return default settings
    return {
        store: {
            name: 'JIMS Jewellery Store',
            address: '123 Main Street, City, Country',
            phone: '+1-234-567-8900',
            email: 'info@jims.com',
            tax_id: 'TAX-123456'
        },
        system: {
            currency: 'USD',
            tax_rate: 0.00,
            low_stock_alert: true,
            backup_enabled: true,
            session_timeout: 3600
        },
        notifications: {
            email_alerts: true,
            low_stock_alerts: true,
            sales_reports: true,
            daily_summary: false
        }
    };
}

function renderSettings(settings) {
    const html = `
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">System Settings</h3>
                <div>
                    <button class="btn btn-success" onclick="saveSettings()">Save Changes</button>
                </div>
            </div>
            <div class="card-body">
                <!-- Settings Tabs -->
                <div class="settings-tabs">
                    <button class="btn btn-outline tab-btn active" onclick="showSettingsTab('store')">Store Settings</button>
                    <button class="btn btn-outline tab-btn" onclick="showSettingsTab('system')">System Configuration</button>
                    <button class="btn btn-outline tab-btn" onclick="showSettingsTab('notifications')">Notifications</button>
                    <button class="btn btn-outline tab-btn" onclick="showSettingsTab('backup')">Backup & Security</button>
                </div>
                
                <!-- Store Settings Tab -->
                <div class="settings-tab-content" id="store-tab">
                    <h4>Store Information</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Store Name</label>
                            <input type="text" class="form-control" id="storeName" value="${settings.store?.name || ''}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="storePhone" value="${settings.store?.phone || ''}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea class="form-control form-textarea" id="storeAddress">${settings.store?.address || ''}</textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="storeEmail" value="${settings.store?.email || ''}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tax ID</label>
                            <input type="text" class="form-control" id="storeTaxId" value="${settings.store?.tax_id || ''}">
                        </div>
                    </div>
                </div>
                
                <!-- System Configuration Tab -->
                <div class="settings-tab-content" id="system-tab" style="display: none;">
                    <h4>System Configuration</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Default Currency</label>
                            <select class="form-select" id="systemCurrency">
                                <option value="USD" ${settings.system?.currency === 'USD' ? 'selected' : ''}>USD - US Dollar</option>
                                <option value="EUR" ${settings.system?.currency === 'EUR' ? 'selected' : ''}>EUR - Euro</option>
                                <option value="GBP" ${settings.system?.currency === 'GBP' ? 'selected' : ''}>GBP - British Pound</option>
                                <option value="CAD" ${settings.system?.currency === 'CAD' ? 'selected' : ''}>CAD - Canadian Dollar</option>
                                <option value="AUD" ${settings.system?.currency === 'AUD' ? 'selected' : ''}>AUD - Australian Dollar</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Default Tax Rate (%)</label>
                            <input type="number" class="form-control" id="systemTaxRate" value="${(settings.system?.tax_rate || 0) * 100}" step="0.1" min="0" max="100">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Session Timeout (minutes)</label>
                            <input type="number" class="form-control" id="sessionTimeout" value="${(settings.system?.session_timeout || 3600) / 60}" min="5" max="480">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Items Per Page</label>
                            <select class="form-select" id="itemsPerPage">
                                <option value="10">10</option>
                                <option value="20" selected>20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="lowStockAlert" ${settings.system?.low_stock_alert ? 'checked' : ''}>
                            <label class="form-check-label" for="lowStockAlert">Enable Low Stock Alerts</label>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications Tab -->
                <div class="settings-tab-content" id="notifications-tab" style="display: none;">
                    <h4>Notification Settings</h4>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="emailAlerts" ${settings.notifications?.email_alerts ? 'checked' : ''}>
                            <label class="form-check-label" for="emailAlerts">Email Notifications</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="lowStockNotifications" ${settings.notifications?.low_stock_alerts ? 'checked' : ''}>
                            <label class="form-check-label" for="lowStockNotifications">Low Stock Alerts</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="salesReports" ${settings.notifications?.sales_reports ? 'checked' : ''}>
                            <label class="form-check-label" for="salesReports">Sales Report Notifications</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="dailySummary" ${settings.notifications?.daily_summary ? 'checked' : ''}>
                            <label class="form-check-label" for="dailySummary">Daily Summary Reports</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Notification Email</label>
                        <input type="email" class="form-control" id="notificationEmail" placeholder="admin@jims.com">
                    </div>
                </div>
                
                <!-- Backup & Security Tab -->
                <div class="settings-tab-content" id="backup-tab" style="display: none;">
                    <h4>Backup & Security</h4>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="backupEnabled" ${settings.system?.backup_enabled ? 'checked' : ''}>
                            <label class="form-check-label" for="backupEnabled">Enable Automatic Backups</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Backup Frequency</label>
                        <select class="form-select" id="backupFrequency">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Manual Operations</h5>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-primary" onclick="createBackup()">Create Backup Now</button>
                            <button class="btn btn-warning" onclick="restoreBackup()">Restore from Backup</button>
                            <button class="btn btn-danger" onclick="clearCache()">Clear System Cache</button>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Security</h5>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-outline" onclick="changePassword()">Change Admin Password</button>
                            <button class="btn btn-outline" onclick="viewLogs()">View System Logs</button>
                            <button class="btn btn-outline" onclick="exportData()">Export All Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    contentArea.innerHTML = html;
}

function showSettingsTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.settings-tab-content').forEach(tab => {
        tab.style.display = 'none';
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    const selectedTab = document.getElementById(`${tabName}-tab`);
    if (selectedTab) {
        selectedTab.style.display = 'block';
    }
    
    // Add active class to clicked button
    event.target.classList.add('active');
}

function saveSettings() {
    console.log('Saving settings...');
    
    // Collect all form data
    const settings = {
        store: {
            name: document.getElementById('storeName').value,
            phone: document.getElementById('storePhone').value,
            address: document.getElementById('storeAddress').value,
            email: document.getElementById('storeEmail').value,
            tax_id: document.getElementById('storeTaxId').value
        },
        system: {
            currency: document.getElementById('systemCurrency').value,
            tax_rate: parseFloat(document.getElementById('systemTaxRate').value) / 100,
            session_timeout: parseInt(document.getElementById('sessionTimeout').value) * 60,
            low_stock_alert: document.getElementById('lowStockAlert').checked,
            backup_enabled: document.getElementById('backupEnabled').checked
        },
        notifications: {
            email_alerts: document.getElementById('emailAlerts').checked,
            low_stock_alerts: document.getElementById('lowStockNotifications').checked,
            sales_reports: document.getElementById('salesReports').checked,
            daily_summary: document.getElementById('dailySummary').checked
        }
    };
    
    // This would typically send to API
    console.log('Settings to save:', settings);
    showSuccess('Settings saved successfully!');
}

function createBackup() {
    console.log('Creating backup...');
    showSuccess('Backup creation started. You will be notified when complete.');
}

function restoreBackup() {
    console.log('Restore backup...');
    showModal('Restore Backup', '<p>Backup restoration will be implemented here</p>');
}

function clearCache() {
    console.log('Clearing cache...');
    if (confirm('Are you sure you want to clear the system cache?')) {
        showSuccess('System cache cleared successfully!');
    }
}

function changePassword() {
    showModal('Change Password', `
        <form id="passwordForm">
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" class="form-control" id="currentPassword" required>
            </div>
            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" id="newPassword" required minlength="8">
            </div>
            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirmPassword" required>
            </div>
        </form>
    `, `
        <button class="btn btn-primary" onclick="updatePassword()">Update Password</button>
        <button class="btn btn-outline" onclick="closeModal('dynamicModal')">Cancel</button>
    `);
}

function updatePassword() {
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;
    
    if (newPass !== confirmPass) {
        showError('Passwords do not match!');
        return;
    }
    
    console.log('Updating password...');
    closeModal('dynamicModal');
    showSuccess('Password updated successfully!');
}

function viewLogs() {
    console.log('Viewing system logs...');
    showModal('System Logs', '<p>System logs will be displayed here</p>');
}

function exportData() {
    console.log('Exporting all data...');
    showSuccess('Data export started. Download will begin shortly.');
}
