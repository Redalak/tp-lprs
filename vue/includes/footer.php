    <footer class="bg-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?= date('Y') ?> Mon Application. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-decoration-none me-3">Mentions légales</a>
                    <a href="#" class="text-decoration-none me-3">Politique de confidentialité</a>
                    <a href="#" class="text-decoration-none">Contact</a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Activer les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Gestion des messages flash
        document.addEventListener('DOMContentLoaded', function() {
            var alertList = document.querySelectorAll('.alert');
            alertList.forEach(function(alert) {
                var closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.addEventListener('click', function() {
                        var bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }
            });
        });
    </script>
</body>
</html>
