<?php
// This footer now correctly closes the <main> tag from the header.
?>
    </main> <!-- Closes the <main> tag from header.php -->

    <footer>
        <p>© <?= date('Y') ?> Mixue Project Team. All Rights Reserved.</p>
    </footer>

    <!-- =====================================================================
         SCRIPTS
         ===================================================================== -->
    
    <!-- Link to your new site-wide JavaScript file -->
    <!-- This handles the sticky header and hamburger menu functionality -->
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>

    <!-- JavaScript for the Profile Page Modal (this can stay here) -->
    <script>
        // Check if the necessary elements exist on the page before running the script
        const openModalBtn = document.getElementById('open-password-modal-btn');
        const closeModalBtn = document.getElementById('close-password-modal-btn');
        const modalOverlay = document.getElementById('password-modal');

        if (openModalBtn && closeModalBtn && modalOverlay) {
            // Function to open the modal
            openModalBtn.addEventListener('click', () => {
                modalOverlay.classList.add('active');
            });

            // Function to close the modal
            const closeModal = () => {
                modalOverlay.classList.remove('active');
            };

            closeModalBtn.addEventListener('click', closeModal);

            // Also close the modal if the user clicks on the dark overlay background
            modalOverlay.addEventListener('click', (event) => {
                if (event.target === modalOverlay) {
                    closeModal();
                }
            });
            
            // Also close the modal if the user presses the 'Escape' key
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modalOverlay.classList.contains('active')) {
                    closeModal();
                }
            });
        }
    </script>

</body>
</html>