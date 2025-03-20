<template>
    <div>
        <h2>Register</h2>
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
                password: '',
            },
            message: '',
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
        },
    },
};
</script>

<style scoped>
form {
    max-width: 400px;
    margin: 20px auto;
}
div {
    margin-bottom: 10px;
}
label {
    display: inline-block;
    width: 100px;
}
</style>
