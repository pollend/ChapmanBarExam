<template>
  <div class="login-container">
    <el-form ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" auto-complete="on" label-position="left">
      <h3 class="title">Chapman Bar Exam</h3>
<!--      <lang-select class="set-language" />-->
      <el-form-item prop="email">
        <span class="svg-container">
          <svg-icon icon-class="user" />
        </span>
        <el-input v-model="loginForm.email" name="email" type="text" auto-complete="on" :placeholder="$t('login.email')" />
      </el-form-item>
      <el-form-item prop="password">
        <span class="svg-container">
          <svg-icon icon-class="password" />
        </span>
        <el-input
          :type="pwdType"
          v-model="loginForm.password"
          name="password"
          auto-complete="on"
          placeholder="password"
          @keyup.enter.native="handleLogin" />
        <span class="show-pwd" @click="showPwd">
          <svg-icon icon-class="eye" />
        </span>
      </el-form-item>
      <el-form-item>
        <el-button :loading="isLoading" type="primary" style="width:100%;" @click.native.prevent="handleLogin">
          Sign in
        </el-button>
      </el-form-item>
      <el-form-item>
        <el-button :disabled="isLoading" @click="handleFederatedLogin" class="federated-login">Chapman Login</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script lang="ts">
import { validEmail } from "../../utils/validate";

import {Component, Provide, Vue, Watch} from "vue-property-decorator";
import "vue-router";
import {Action, namespace} from "vuex-class";
import {LoginError} from "../../store/modules/auth";

const validateEmail = (rule: any, value: string, callback: any) => {
  if (!validEmail(value)) {
    callback(new Error('Please enter the correct email'));
  } else {
    callback();
  }
};

const validatePass = (rule: any, value: string, callback: any) => {
  if (value.length < 4) {
    callback(new Error('Password cannot be less than 4 digits'));
  } else {
    callback();
  }
};
const authModule = namespace('auth')

@Component
export default class Login extends Vue {
  @authModule.Action('login') authLogin: any;
  @authModule.Getter('isLoading') isLoading: boolean;
  @authModule.Getter('loginError') loginError: LoginError;

  @Provide() loginRules = {
    email: [{required: true, trigger: 'blur', validator: validateEmail}],
    password: [{required: true, trigger: 'blur', validator: validatePass}]
  };
  @Provide() loginForm = {
    email: '',
    password: ''
  };

  @Provide() pwdType = 'password';
  @Provide() redirect: string = null;

  @Watch("loginError")
  onLoginError(err: LoginError) {
    this.$message({
      message: err.message,
      type: 'error'
    });
  }

  @Watch('$route', {immediate: true})
  onRouteChange(route: any) {
    this.redirect = route.query && route.query.redirect;
  }

  showPwd() {
    if (this.pwdType === 'password') {
      this.pwdType = '';
    } else {
      this.pwdType = 'password';
    }
  }

  handleFederatedLogin() {
    window.location.replace('/saml/login');
  }

  handleLogin() {
    let form: any = this.$refs.loginForm;
    form.validate((valid: any) => {
      if (valid) {
        this.authLogin(this.loginForm).then(() => {
          this.$router.push({path: this.redirect || '/'});
        }).catch(() => {
        });
      } else {
        console.log('error submit!!');
        return false;
      }
    });
  }
};
</script>

<style rel="stylesheet/scss" lang="scss">
$bg:#2d3a4b;
$light_gray:#eee;

/* reset element-ui css */
.login-container {
  .el-input {
    display: inline-block;
    height: 47px;
    width: 85%;
    input {
      background: transparent;
      border: 0px;
      -webkit-appearance: none;
      border-radius: 0px;
      padding: 12px 5px 12px 15px;
      color: $light_gray;
      height: 47px;
      &:-webkit-autofill {
        -webkit-box-shadow: 0 0 0px 1000px $bg inset !important;
        -webkit-text-fill-color: #fff !important;
      }
    }
  }
  .el-form-item {
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    color: #454545;
  }
}

</style>

<style rel="stylesheet/scss" lang="scss" scoped>
$bg:#2d3a4b;
$dark_gray:#889aa4;
$light_gray:#eee;
.login-container {
  position: fixed;
  height: 100%;
  width: 100%;
  background-color: $bg;
  .login-form {
    position: absolute;
    left: 0;
    right: 0;
    width: 520px;
    max-width: 100%;
    padding: 35px 35px 15px 35px;
    margin: 120px auto;
  }
  .federated-login{
    border-color: #ca003a;
    color: white;
    background: #A50034;
    font-size: 1.5rem;
    width: 100%;
  }
  .svg-container {
    padding: 6px 5px 6px 15px;
    color: $dark_gray;
    vertical-align: middle;
    width: 30px;
    display: inline-block;
  }
  .title {
    font-size: 26px;
    font-weight: 400;
    color: $light_gray;
    margin: 0px auto 40px auto;
    text-align: center;
    font-weight: bold;
  }
  .show-pwd {
    position: absolute;
    right: 10px;
    top: 7px;
    font-size: 16px;
    color: $dark_gray;
    cursor: pointer;
    user-select: none;
  }
  .set-language {
    color: #fff;
    position: absolute;
    top: 40px;
    right: 35px;
  }
}
</style>
