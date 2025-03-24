<template>
    <div>
        <h2>Login</h2>
        <form @submit.prevent="submitForm">
            <div>
                <label>Name:</label>
                <input v-model="form.name" type="text" required />
            </div>
            <div>
                <label>Email:</label>
                <input v-model="form.email" type="email" required />
            </div>
            <div>
                <label>Password:</label>
                <input v-model="form.password" type="password" required />
            </div>
            <button type="submit">Register</button>
            <p v-if="message">{{ message }}</p>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'Register',
    data() {
        return {
            form: {
                name: '',
                email: '',
                password: ''
            },
            message: ''
        };
    },
    methods: {
        async submitForm() {
            try {
                const response = await axios.post('/api/register', this.form);
                this.message = response.data.message;
                this.form = { name: '', email: '', password: '' }; // 清空表单
            } catch (error) {
                this.message = error.response?.data?.message || 'Registration failed';
            }
        }
    }
};
</script>

<style scoped lang="scss">
.login-body-wrap {
    width: 100%;
    min-width: 1440px;
    height: 100%;
    min-height: 100vh;
    background: url('https://cdn.toodudu.com/2024/05/24/ZOIWKKG4TKVDs3cDXqhXLNlW3atMO7JiJZ1M6Jfe.png') center center no-repeat;
    background-size: cover;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    overflow-x: hidden;
}

.wrap {
    width: 1440px;
    overflow: hidden;
    margin: 0 auto;
    height: 130px;
}

.header-wrap {
    height: auto !important;
}

.logo {
    padding: 50px 0px 0;
    font-size: 0;
    float: left;
}

.logo a img {
    height: 70px;
    width: auto;
    font-size: 0;
}

.bg-img {
    height: 600px;
    width: 100%;
}

.center-wrap .wrap {
    height: auto;
    min-height: 850px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.center-wrap .wrap .center-bg {
    width: 1000px;
    height: 700px;
    background: url('https://testcdn.ibisaas.com/2025/02/08/5bpXJOozRKBAO7rqv77jAmDX6id5Bo8C2YYkonyQ.png') center no-repeat;
    background-size: cover;
}

.center-wrap .wrap .center-bg-a {
    width: 100%;
    height: 100%;
    display: block;
}

.center-wrap .wrap .form-wrap {
    width: 400px;
    height: 450px;
    background: #fff;
    border-radius: 10px;
    position: relative;
    box-shadow: 0px 4px 20px 0px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-sizing: border-box;
    margin-right: 20px;
    /* margin-right: 10px; */
}

.switch-type {
    width: fit-content;
    position: absolute;
    right: 6px;
    top: 6px;
    height: 71px;
    z-index: 2;
}

.switch-icon {
    width: 71px;
    height: 71px;
    position: absolute;
    top: 0;
    right: 0;
    overflow: hidden;
    background: linear-gradient(45deg, transparent, transparent 50%, #F7F7F9 50%, #F7F7F9 100%);
    border-top-right-radius: 10px;
    border-bottom-left-radius: 100px;
    cursor: pointer;
    z-index: 1;
}

.switch-icon::before {
    content: '';
    width: 71px;
    height: 71px;
    background: linear-gradient(45deg, #fff, #fff 50%, transparent 50%, transparent 100%);
    border-top-right-radius: 10px;
    border-bottom-left-radius: 100px;
    position: absolute;
    top: 0;
    right: 0;
    z-index: 4;
}

/* .switch-icon:hover {
    background: linear-gradient(45deg, transparent, transparent 50%, #333 50%, #333 100%);
    opacity: 0.7;
} */

.switch-icon em.iconfont {
    font-size: 36px;
    color: #333;
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 3;
}

/* .switch-icon:hover em.iconfont {
    color: #fff;
} */

#type1 img:first-of-type {
    width: 110px;
    position: relative;
    top: -10px;
    left: -73px;
    animation: anicode infinite 1s;
    cursor: pointer;
}

#type2 img:first-of-type {
    width: 147px;
    position: relative;
    top: -10px;
    left: -73px;
    animation: anicode1 infinite 1s;
    cursor: pointer;
}

.common {
    padding: 48px 55px 0;
    position: relative;
}

.common #message {
    position: absolute;
    bottom: 10px;
    left: 55px;
}

.common .logo-text {
    color: #333;
    font-size: 20px;
    /* line-height: 45px; */
    font-weight: bolder;
    text-align: center;
    margin-bottom: 8px;
}

.common .tab {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.common .tab .tab-item-border {
    width: 30px;
    height: 3px;
    transition: transform .3s cubic-bezier(.645, .045, .355, 1);
    background: var(--main-color);
    position: absolute;
    left: 0;
    bottom: 0;
}

.common .tab .tab-item {
    line-height: 23px;
    padding: 10px 0 7px;
    margin: 0 30px;
    text-align: center;
    font-size: 14px;
    font-weight: bolder;
    color: #333;
    position: relative;
    cursor: pointer;
}

.common .tab .tab-item.active {
    color: var(--main-color);
}

/* .common .tab .tab-item.active::after {
    content: '';
    width: 30px;
    height: 3px;
    background: var(--main-color;
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
} */

.common .common-form {
    width: 100%;
}

input {
    background: none;
    outline: none;
    border: none;
}

input:focus {
    border: none;
}

.common .common-form input {
    width: 100%;
    margin-top: 24px;
    height: 46px;
    box-sizing: border-box;
    padding: 0 20px;
    background: none;
    outline: none;
    background: #F7F7F9;
    border: none;
    border-radius: 8px;
    color: #333;
    font-size: 14px;
}

.common .common-form input:first-child {
    margin-top: 40px;
}

.common .common-form input::placeholder {
    color: #B0B0B0;
}

.common .common-form input::-webkit-input-placeholder {
    color: #B0B0B0;
}

.common .common-form input::-moz-placeholder {
    color: #B0B0B0;
}

.common .common-form input:-ms-input-placeholder {
    color: #B0B0B0;
}

.common .common-form button {
    padding: 0;
    margin: 24px auto 40px;
    border: 1px solid transparent;
    height: 46px;
    width: 100%;
    color: #fff;
    font-size: 16px;
    font-weight: bolder;
    background: var(--main-color);
    opacity: 1;
    border-radius: 8px;
    outline: none;
    cursor: pointer;
}

.common .common-form button:disabled {
    background: #000;
    opacity: 0.2;
    cursor: not-allowed;
}

.other-entry {
    display: flex;
    justify-content: center;
}

.other-entry a {
    padding: 0 16px;
    color: #767676;
    font-size: 14px;
    line-height: 20px;
    cursor: pointer;
    text-align: center;
    position: relative;
}

.other-entry a:not(:last-child)::before {
    content: '';
    display: block;
    width: 0;
    height: 10px;
    border-left: 1px solid #D8D8D8;
    position: absolute;
    right: 0;
    top: 5px;
}


.common .phone {
    height: 46px;
    margin-top: 40px;
    position: relative;
    background: #F7F7F9;
    border-radius: 8px;
}

.phone select {
    border: none;
    outline: 0;
    padding-left: 7px;
    cursor: pointer;
    appearance: none;
    -moz-appearance: none;
    -webkit-appearance: none;
    background: transparent;
    background-size: 24px;
    background-repeat: no-repeat;
    background-position: 48px 8px;
    width: 71px;
    height: 46px;
    line-height: 46px;
    float: left;
    border-radius: 8px 0 0 8px;
    border-right: none;
    color: #666;
    outline: none;
    position: relative;
    z-index: 2;
    opacity: 0;
}

.phone::after {
    content: '';
    border-top: 5px solid #ccc;
    border-bottom: 5px solid transparent;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    position: absolute;
    top: 20px;
    left: 54px;
    z-index: 1;
}

.phone span {
    width: 71px;
    height: 46px;
    line-height: 46px;
    position: absolute;
    top: 0;
    left: 0;
    color: #333;
    z-index: 2;
    font-size: 14px;
    text-align: center;
    box-sizing: border-box;
    padding-right: 8px;
}

.phone span::before {
    content: '';
    display: block;
    width: 0;
    height: 18px;
    border-left: 1px solid #D8D8D8;
    position: absolute;
    top: 14px;
    right: 0;
}

.common .phone input {
    margin-top: 0;
    width: calc(100% - 71px);
    float: left;
    border-radius: 0 8px 8px 0;
    padding-right: 38px;
    box-sizing: border-box;
    background: url('https://cdn.toodudu.com/2024/05/24/eN94vgZPXVgwun4hrumRIbmnWW2BiqBkkGCkqWvF.png') center no-repeat;
    background-size: 18px 26px;
    background-position: 230px center;
}

.common .code {
    position: relative;
    width: 100%;
}

.common .code input {
    margin-top: 24px !important;
}

.common .code span {
    line-height: 23px;
    position: absolute;
    right: 10px;
    top: 36px;
    font-size: 16px;
    cursor: pointer;
    color: #CECECE;
}

.common .code span.active {
    color: var(--main-color);
}

#common-form-phone {
    display: none;
}

#code-login {
    display: none;
    position: relative;
    padding: 48px 0 0;
}

#code-login #codeMessage {
    width: 100%;
    position: absolute;
    bottom: 10px;
    left: 0;
    text-align: center;
}

#code-login .login-fail {
    display: none;
}

#code-login .sao-success {
    display: none;
}

.code-login h5 {
    margin: 0;
    padding-bottom: 20px;
    line-height: 45px;
    text-align: center;
    color: #333;
    font-size: 20px;
}

.code-wrap {
    text-align: center;
}

.code-wrap .img-cont > img {
    width: 150px;
    height: 150px;
    background: rgba(0, 0, 0, 0.02);
}

.code-wrap .img-cont-hover {
    cursor: pointer;
}

.code-wrap .img-cont #code-hover, .code-wrap .img-cont-hover #code-hover {
    display: none;
}

.code-wrap .img-cont-hover:hover img {
    margin: 0 6px;
}

.code-wrap .img-cont-hover:hover #code-hover {
    width: 150px;
    /* height: 127px; */
    display: block;
}

.code-wrap .img-cont-hover:hover #code-html {
    width: 150px;
    /* height: 127px; */
}

.code-wrap .img-cont {
    width: 150px;
    height: 150px;
    display: inline-block;
    margin: 0 auto;
}

.code-wrap .img-cont-hover:hover {
    display: flex;
    width: 340px;
    height: 150px;
    padding-top: 4px;
    justify-content: center;
}

.code-wrap h6 {
    font-size: 14px;
    color: #333;
    font-weight: normal;
    line-height: 21px;
    margin: 8px auto 24px;
}

.tedian {
    display: flex;
    flex-wrap: nowrap;
    padding: 0 0 44px;
    justify-content: center;
}

.tedian div {
    /*width: 30%;*/
    padding: 0 12px;
    line-height: 24px;
    font-size: 14px;
    color: #333;
    position: relative;
    display: flex;
    align-items: center;
}

.tedian div img {
    width: 24px;
    height: 24px;
}

.tedian div:not(:first-child)::before {
    content: '';
    display: block;
    width: 0;
    height: 10px;
    border-left: 1px solid #D8D8D8;
    position: absolute;
    left: 0;
    top: 6px;
}

.other-entry1 {
    display: none;
}

.img-cont {
    position: relative;
}

.login-fail {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    left: 0;
    top: 0;
}

.login-fail p {
    color: #fff;
    line-height: 24px;
    font-size: 14px;
}

.login-fail p:first-of-type {
    padding-top: 30px;
}

.login-fail p span {
    font-size: 12px;
    display: inline-block;
    width: 60px;
    height: 24px;
    line-height: 24px;
    background: var(--main-color);
    border-radius: 4px;
    cursor: pointer;
}

.sao-success {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
}

#sao-tip span {
    color: var(--main-color);
}

.sao-success img {
    width: 73px;
    padding-top: 20px;
}

.sao-success p {
    margin: 0;
    padding-top: 15px;
    color: #333;
    font-size: 14px;
}

#type2 {
    display: none;
}

#code-login {
    display: none;
}

.footer-login {
    width: 1440px;
    margin: 0 auto;
    padding-bottom: 42px;
}

.footer-login .wrap p a {
    font-size: 14px;
    text-decoration: none;
    color: #767676;
    line-height: 30px;
    display: inline-block;
}

.footer-login .wrap p span {
    margin: 0 5px 0 2px;
    transform: scale(0.7);
    display: inline-block;
    color: #767676;
    line-height: 30px;
}

.footer-login .wrap p:first-of-type span {
    position: relative;
    top: 1px;
}

.footer-login .wrap p a:hover {
    color: var(--main-color);
    /* text-decoration: underline; */
}

.footer-login .wrap {
    overflow: inherit;
    text-align: center;
    height: auto;
}

.footer-login .wrap > div {
    text-align: center;
    width: 100%;
}

.footer-login .wrap > div span {
    color: #767676;
    font-size: 14px;
    display: inline-block;
    height: 20px;
    padding: 0 20px;
}

.footer-login .wrap ul {
    padding-top: 15px;
    list-style: none;
    max-width: 100%;
    display: inline-block;
}

.footer-login .wrap ul li {
    float: left;
}

.footer-login .wrap ul li img {
    width: auto;
    height: 36px;
    margin: 0 12px;
}

#message, #codeMessage {
    color: #f10c0f;
    font-size: 12px;
    padding: 0 0px;
}

.other-entry a {
    text-decoration: none;
}

.other-entry a:hover {
    color: var(--main-color);
}

@keyframes toa {
    0% {
        opacity: 0;

    }
    15% {
        opacity: 1;
        top: 80px;
    }
    70% {
        opacity: 1;
        top: 80px;
    }
    80% {
        opacity: 0;
        top: 80px;
    }
    100% {
        opacity: 0;
        top: -40px;
    }

}

.toast {
    position: fixed;
    left: 0;
    right: 0;
    top: -40px;
    /*top:0;*/
    height: 40px;
    text-align: center;
    width: auto;
    margin: auto;
    /*animation: toa linear 3s;*/
    z-index: 10;
}

.toast p {
    display: inline-block;
    max-width: 290px;
    border-radius: 4px;
    background: var(--main-color);
    height: 40px;
    line-height: 40px;
    padding: 0 10px;
    color: #fff;
    margin: 0;
    font-size: 14px;
}

#code-btn {
    color: #CECECE;
    font-size: 14px;
    font-weight: 500;
    display: inline-block;
    background: #F7F7F9;
}

.login-adlink a {
    position: absolute;
    width: 630px;
    height: 400px;
    left: -780px;
    top: -50px;
}

@media screen and (max-width: 1440px) and (min-width: 1024px) {
    .login-body-wrap {
        min-width: 100%;
    }

    .wrap {
        width: 100vw;
    }

    .center-wrap .wrap .center-bg {
        width: calc(100% - 480px);
    }

    .footer-login {
        width: 100%;
    }
}

@media screen and (max-width: 1024px) {
    .wrap {
        width: fit-content;
    }

    .login-body-wrap {
        min-width: 100%;
    }

    .center-wrap .wrap {
        justify-content: center;
    }

    .center-wrap .wrap .form-wrap {
        margin-right: 0;
    }

    .center-wrap .wrap .center-bg, .center-wrap .wrap .center-bg-a {
        display: none;
    }

    .footer-login {
        width: 100%;
    }
}

.password-tip-wrap {
    width: 100vw;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.3);
    z-index: 1111;
    display: none;
}

.password-tip-wrap .content-wrap {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.password-tip-wrap .tip-inner {
    width: 420px;
    height: 200px;
    background: #fff;
    border-radius: 5px;
    padding: 30px;
    box-sizing: border-box;
}

.password-tip-wrap .tip-inner p {
    font-size: 16px;
    color: #333;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    margin: 0;
    line-height: 22px;
    margin-bottom: 20px;
}

.password-tip-wrap .tip-inner p img {
    margin-right: 7px;
}

.password-tip-wrap .tip-inner h5 {
    font-size: 14px;
    color: #666;
    text-align: center;
    margin: 0;
    line-height: 20px;
    font-weight: normal;
}

.password-tip-wrap .tip-inner .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 24px;
}

.password-tip-wrap .tip-inner .btn a {
    text-underline: none;
    color: #3d3d3d;
    border: 1px solid #DDDFE6;
    border-radius: 2px;
    display: inline-block;
    height: 32px;
    line-height: 32px;
    width: fit-content;
    text-align: center;
    margin: 0 10px;
    text-decoration: none;
    font-size: 12px;
    padding: 0 16px;
}

.password-tip-wrap .tip-inner .btn a.primary {
    color: #fff;
    border-color: #0062EA;
    background: #0062EA;
}
</style>
