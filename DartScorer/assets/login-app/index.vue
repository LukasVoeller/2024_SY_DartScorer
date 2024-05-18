<template>
  <!-- ======= Login ======= -->
  <div class="container" style="padding-top: 0px">
    <div class="row justify-content-center">
      <img id="sw-login-logo" src="/homepage/assets/img/Logo-Scorin-Wizard_edit.png" alt="Logo.png">
    </div>

    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <!--
          <div class="card-header" id="sw-login-card-header">Login</div>
          -->
          <div class="card-body">

            <!-- <form method="post" action="/login" @submit="login"> -->
            <!-- <form @submit="onSubmit" action="/login"> -->
            <!-- <form method="post" action="/login"> -->
            <form method="post" action="/login" @submit.prevent="login" ref="loginForm">
              <div class="mb-3">
                <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="_username"
                    v-model="username"
                    placeholder="Username"
                required
                >
              </div>
              <div class="mb-3">
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="_password"
                    v-model="password"
                    placeholder="Password"
                required
                >
              </div>

              <div class="row">
                <div class="col-8">
                  <button id="sw-login-btn" style="margin-right: 10px" type="submit" :disabled="isSubmitting">
                    Login
                  </button>
                </div>
                <div class="col-4">
                  <button id="sw-info-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    Info
                  </button>
                </div>
              </div>

              <div v-if="loginError" class="alert alert-warning mt-3" role="alert">
                {{ loginError }}  <!-- Display the error message -->
              </div>

              <div style="display: flex; justify-content: center">
                <VueSpinnerDots v-if="isSubmitting" size="40" color="black" />
              </div>
            </form>


            <div class="collapse" id="collapseExample">

              <div class="row justify-content-center">
                <div class="col">
                  <div class="card" style="margin-top: 15px;">
                    <div class="card-header" id="sw-login-info-card-header">
                      Tech Stack
                    </div>
                    <div class="card-body">
                      <ul style="padding-left: 15px">
                        <li>Backend:</li>
                        <ul>
                          <li>Symfony 6.4</li>
                          <li>PHP 8.2</li>
                          <li>MySQL 8.0</li>
                        </ul>
                        <li>Frontend:</li>
                        <ul>
                          <li>Vue.js 3.2</li>
                          <li>Bootstrap 5.3</li>
                        </ul>
                      </ul>
                    </div>
                  </div>
                </div>

                <div class="col">
                  <div class="card" style="margin-top: 15px;">
                    <div class="card-header" id="sw-login-info-card-header">
                      Features
                    </div>
                    <div class="card-body">
                      <ul style="padding-left: 15px">
                        <li>Deployer PHP</li>
                        <li>APIs secured with JWT</li>
                      </ul>
                    </div>
                  </div>
                </div>

              </div>
              <p style="float: right; padding-top: 10px">v0.9.0</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ======= RDA ======= -->
  <div class="container" style="padding-top: 30px; padding-bottom: 30px">
    <div class="row justify-content-center">
      <div class="col-md-3 col-sm-6 align-self-start">
        <p style="color: white"><strong>In association with:</strong></p>
      </div>

      <div class="col-md-3 col-sm-6">
        <img src="homepage/assets/img/logo-rda.png" alt="" style="max-width: 30%; float: right">
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { VueSpinnerDots } from "vue3-spinners";

export default {
  name: 'LoginComponent',

  components: {
    VueSpinnerDots,
  },

  data() {
    return {
      username: '',  // New data properties for binding
      password: '',
      isSubmitting: false,
      loginError: null,
    };
  },

  methods: {
    async login() {
      this.isSubmitting = true;
      this.loginError = null;

      //console.log("Login with Username: " + this.username + " Password: " + this.password);

      try {
        const response = await axios.post('/api/login_check', {
          username: this.username,
          password: this.password,
        });

        const token = response.data.token;
        localStorage.setItem('jwt_token', token);
        //console.log("Tokenb: " + token);
        this.$refs.loginForm.submit();

      } catch (error) {
        // Handle login failure
        if (error.response && error.response.status === 401) {
          this.loginError = 'Invalid username or password.';
          this.isSubmitting = false;
        } else {
          this.loginError = 'An error occurred during login.';
          this.isSubmitting = false;
        }
      } finally {
        //await wait(2000);
        //this.isSubmitting = false;
      }
    },
  }
}
</script>
