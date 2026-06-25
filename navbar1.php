<nav class="navbar navbar-expand-lg custom-navbar">

        <div class="container">

            <!-- LOGO -->

            <a class="navbar-brand logo" href="index.php">

                <span class="leo">Leo</span>

                <span class="ampersand">&</span>

                <span class="friends">Friends</span>

            </a>

            <!-- BOTÓN RESPONSIVE -->

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <!-- MENÚ CENTRADO -->

                <ul class="navbar-nav mx-auto menu-center">

                    <li class="nav-item">

                        <a class="nav-link" href="tus-mascotas.php">
                            Tus Mascotas
                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link" href="sobre-nosotros.php">
                            Sobre Nosotros
                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link" href="#">
                            Paquetes Salvajes
                        </a>

                    </li>

                    <li class="nav-item">

                        <a class="nav-link" href="resenas.php">
                            Reseñas
                        </a>

                    </li>

                </ul>

                <!-- DERECHA -->

                <div class="nav-buttons">

                    <?php if (isset($_SESSION['userID'])): ?>

                        <div class="dropdown">

                            <a
                            class="btn profile-btn dropdown-toggle"

                            href="#"

                            data-bs-toggle="dropdown">

                                <i class="fa-solid fa-circle-user"></i>

                                <?php echo $_SESSION['nombre_nino']; ?>

                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>

                                    <a class="dropdown-item"

                                    href="profile.php">

                                        Perfil

                                    </a>

                                </li>

                                <li>

                                    <hr class="dropdown-divider">

                                </li>

                                <li>

                                    <a

                                    class="dropdown-item text-danger"

                                    href="php/logout.php">

                                        Cerrar sesión

                                    </a>

                                </li>

                            </ul>

                        </div>

                    <?php else: ?>

                        <a class="btn login-btn"

                        href="login.html">

                            Iniciar sesión

                        </a>

                        <a class="btn register-btn"

                        href="register.html">

                            Registrarse gratis

                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </nav>