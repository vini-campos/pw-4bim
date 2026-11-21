    <hr>
    </main> <!-- /container -->

    <footer class="container">
        <?php $dt = new DateTime("now", new DateTimeZone("America/Sao_Paulo")); ?>
        <p>&copy;2026 à <?= $dt->format("Y")?> - Vinicius Raphael de caio Henrique</p>
    </footer>

    <script src="<?php echo BASEURL; ?>assets/js/jquery-4.0.0.min.js"></script>
    <script src="<?php echo BASEURL; ?>assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASEURL; ?>assets/js/all.min.js"></script>
    <script src="<?php echo BASEURL; ?>assets/js/main.js"></script>
    </body>

</html>