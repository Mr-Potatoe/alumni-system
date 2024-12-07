    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/custom.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all dropdowns
        var dropdowns = document.querySelectorAll('.dropdown-trigger');
        M.Dropdown.init(dropdowns);
        
        // Initialize all selects
        var selects = document.querySelectorAll('select');
        M.FormSelect.init(selects);
        
        // Initialize mobile navigation
        var sidenavs = document.querySelectorAll('.sidenav');
        M.Sidenav.init(sidenavs);
        
        // Initialize date pickers
        var datepickers = document.querySelectorAll('.datepicker');
        M.Datepicker.init(datepickers, {
            format: 'yyyy-mm-dd',
            yearRange: 50
        });
    });
    </script>
</body>
</html>
