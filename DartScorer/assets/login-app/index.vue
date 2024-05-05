<template>
  <!-- ======= Login ======= -->
  <div class="container" style="padding-top: 30px">
    <div class="row justify-content-center">
      <div class="d-flex justify-content-center"> <!-- Flex container for centering -->
        <h1>Welcome to my Dart App!</h1>
      </div>

      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-header">Login</div>
          <div class="card-body">

            <!-- <form method="post" action="/login" @submit="login"> -->
            <!-- <form @submit="onSubmit" action="/login"> -->
            <!-- <form method="post" action="/login"> -->
            <form method="post" action="/login" @submit.prevent="login" ref="loginForm">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="_username"
                    v-model="username"
                required
                >
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="_password"
                    v-model="password"
                required
                >
              </div>
              <button id="custom-btn" style="margin-right: 10px" type="submit" class="btn btn-primary" :disabled="isSubmitting">Login</button>

              <button id="custom-btn" class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                Info
              </button>

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
                    <div class="card-header">
                      Tech Stack
                    </div>
                    <div class="card-body">
                      <ul style="padding-left: 15px">
                        <li>Backend:</li>
                        <ul>
                          <li>Symfony 6.4.6</li>
                          <li>PHP 8.2.18</li>
                          <li>MySQL 8.0.36</li>
                        </ul>
                        <li>Frontend:</li>
                        <ul>
                          <li>Vue.js 3.2.14</li>
                          <li>Bootstrap 5.3.3</li>
                        </ul>
                      </ul>
                    </div>
                  </div>
                </div>

                <div class="col">
                  <div class="card" style="margin-top: 10px;">
                    <div class="card-header">
                      Features
                    </div>
                    <div class="card-body">
                      <ul style="padding-left: 15px">
                        <li>Custom deployer php</li>
                        <li>Custom Symfony commands</li>
                        <li>User login- and role management</li>
                        <li>APIs secured with JWT</li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ======= RDA ======= -->
  <div class="container" style="padding-top: 30px">
    <div class="row justify-content-center">
      <div class="col-md-3 col-sm-6 align-self-start">
        <p><strong>In association with:</strong></p>
      </div>

      <div class="col-md-3 col-sm-6 d-flex justify-content-end">
        <img src="homepage/assets/img/logo-rda.png" alt="" style="width: 50%">
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
