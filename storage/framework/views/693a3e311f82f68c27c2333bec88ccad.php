<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartDesk - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">SmartDesk Dashboard</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <!-- Welcome Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-4">✅ Welcome to SmartDesk!</h2>
                        <p class="text-gray-600 mb-4">Your modular business dashboard is up and running!</p>

                        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        <strong>System Status:</strong> All services are running correctly!
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-blue-900 mb-2">📋 Tickets</h3>
                                <p class="text-blue-700 text-sm">Manage support tickets and track issues</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-purple-900 mb-2">📄 Documents</h3>
                                <p class="text-purple-700 text-sm">Upload and manage documents with versioning</p>
                            </div>
                            <div class="bg-orange-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-orange-900 mb-2">🔔 Notifications</h3>
                                <p class="text-orange-700 text-sm">Receive real-time system notifications</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">System Information</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Application</dt>
                                <dd class="mt-1 text-sm text-gray-900">SmartDesk v1.0</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Framework</dt>
                                <dd class="mt-1 text-sm text-gray-900">Laravel <?php echo e(app()->version()); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">PHP Version</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo e(PHP_VERSION); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Environment</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo e(app()->environment()); ?></dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Links</h3>
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600">📝 <strong>Documentation:</strong> Check CLAUDE.md for development commands</p>
                            <p class="text-sm text-gray-600">📧 <strong>MailHog:</strong> <a href="http://localhost:8025" target="_blank" class="text-blue-600 hover:underline">http://localhost:8025</a></p>
                            <p class="text-sm text-gray-600">🔗 <strong>API Endpoints:</strong> <code class="bg-gray-100 px-2 py-1 rounded">/api/*</code></p>
                        </div>
                    </div>
                </div>

                <!-- Demo Users -->
                <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Demo User Credentials</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li><strong>Admin:</strong> admin@smartdesk.com / password</li>
                                    <li><strong>Manager:</strong> manager@smartdesk.com / password</li>
                                    <li><strong>Employee:</strong> employee@smartdesk.com / password</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white shadow mt-8">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    SmartDesk - Modular Business Dashboard | Built with Laravel & Docker
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
<?php /**PATH /var/www/resources/views/dashboard.blade.php ENDPATH**/ ?>