<script setup>
import {onMounted, ref} from "vue";
import {http} from "@/utils.js";

let products = ref([]);
let page = ref(1);
let hours = ref(4);
let bearerToken = ref('');
let email = ref('');
let password = ref('');

const getProducts = async (selectedPage = 1) => {
    page.value = selectedPage;
    await http.get(`/api/products?page=${selectedPage}`)
        .then(async (res) => {
            if (res.status === 200) {
                products.value = res.data.data;
            }
        });
}

const order = async (product_id, type = 'buy', hours = 4) => {
    if (!bearerToken) {
        alert('Авторизуйтесь!');
    }
    const config = {
        headers: { Authorization: `Bearer ${bearerToken.value}` }
    };
    await http.post(`/api/orders/create`, {
        product_id,
        type,
        hours
    }, config).then(async (res) => {
        if (res.status === 200) {
            alert(res.data.message);
        }
    }).catch(res => {
        if (typeof res.response.data.errors !== 'undefined') {
            alert(res.response.data.errors);
        }
        if (typeof res.response.data.message !== 'undefined') {
            alert(res.response.data.message);
        }
    });
}

const login = async (email, password) => {
    await http.post(`/api/login`, {
        email,
        password
    }).then(async (res) => {
        if (res.status === 200) {
            let token = res.data.authorization.token;
            bearerToken.value = token;
        }
    }).catch(res => {
        console.log(res);
    });
}

onMounted(() => {
    getProducts(page.value);
});
</script>

<template>
    <template v-if="!bearerToken">
        <div class="p-6">
            <div>
                <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                <div class="mt-2">
                    <input v-model="email" id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Пароль</label>
                </div>
                <div class="mt-2">
                    <input v-model="password" id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
            </div>
            <div class="mt-2">
                <button @click.prevent="login(email, password)" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Войти</button>
            </div>
        </div>
    </template>
    <template v-else>
        Вы авторизовались как: {{ email }}
    </template>

    <div class="bg-white">

        <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">Каталог</h2>
            <h3 class="text-md font-bold tracking-tight text-gray-900">Аренда на:</h3>
            <select v-model="hours">
                <option :selected="hours == 4" value=4>4 часа</option>
                <option :selected="hours == 8" value=8>8 часов</option>
                <option :selected="hours == 12" value=12>12 часов</option>
                <option :selected="hours == 24" value=24>24 часа</option>
            </select>

            <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
                <template v-for="product in products">
                    <div class="group relative">
                        <div class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-md bg-gray-200 lg:aspect-none group-hover:opacity-75 lg:h-80">
                            <img :src="product.image"
                                 :alt="product.title"
                                 class="h-full w-full object-cover object-center lg:h-full lg:w-full"
                            />
                        </div>
                        <div class="mt-4">
                            <div>
                                <h3 class="text-sm text-gray-700">
                                    <a href="#">
                                        <!-- <span aria-hidden="true" class="absolute inset-0"></span> -->
                                        {{ product.title }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">{{ product.description }}</p>
                            </div>
                            <p class="text-sm font-medium text-gray-900 mt-1">Цена покупки: {{ product.price_buy }}</p>
                            <p class="text-sm font-medium text-gray-900 mt-1">Цена аренды (в час): {{ product.price_rent }}</p>

                        </div>
                        <button @click.prevent="order(product.id)" class="inline-flex justify-center rounded-lg text-sm font-semibold py-3 px-4 bg-slate-900 text-white hover:bg-slate-700 pointer-events-auto mr-2">Купить</button>
                        <button @click.prevent="order(product.id,'rent', hours)" class="inline-flex justify-center rounded-lg text-sm font-semibold py-3 px-4 bg-slate-900 text-white hover:bg-slate-700 pointer-events-auto">Арендовать</button>
                    </div>
                </template>
            </div>
            <div class="mt-3 flow-root">
                <a
                    v-if="page-1>0"
                    class="cursor-pointer float-left bg-white font-semibold py-2 px-4 border rounded shadow-md text-slate-800"
                    @click.prevent="getProducts(page-1)"
                >
                    Предыдущая
                </a>
                <a
                    class="cursor-pointer float-right bg-white font-semibold py-2 px-4 border rounded shadow-md text-slate-800"
                    @click.prevent="getProducts(page+1)"
                >
                    Следующая
                </a>
            </div>
        </div>
    </div>
</template>
