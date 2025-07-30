    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Debug script
        console.log('Admin footer loaded');
        
        // Add click event listeners to all nav links
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    console.log('Link clicked:', this.href);
                });
            });
        });
        
        // Check for any JavaScript errors
        window.addEventListener('error', function(e) {
            console.error('JavaScript error:', e.error);
        });
    </script>
</body>
</html>