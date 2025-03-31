<main style="width: 100vw; height: 100vh">
    <div id="login-panel">
        <?php $this->load->view("components/cat") ?>
        <div class="form-wrapper">
            <h1 class="title">Hi, cat-lover</h1>
            <p class="subtitle">Please login to the system</p>

            <form name="login" class="login-form" method="POST" action="">
                <fieldset>
                    <div class="form-column">
                        <label class="form-label" for="username">Username</label>
                        <input class="form-input" type="text" name="username" id="username" required>
                    </div>
                    <div class="form-column">
                        <label class="form-label" for="password">Password</label>
                        <input class="form-input" type="password" name="password" id="password" required>
                    </div>

                    <button type="submit" class="submit-button" style="margin-top: 2em; margin-bottom: 1em;">Submit</button>
                </fieldset>
            </form>
        </div>
    </div>
</main>