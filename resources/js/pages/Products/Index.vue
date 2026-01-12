<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, reactive, watchEffect } from 'vue';

type Product = {
    id: number;
    name: string;
    image_url: string | null;
    price: string;
    stock_quantity: number;
};

const props = defineProps<{
    products: Product[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];

const page = usePage();
const quantities = reactive<Record<number, number>>({});

watchEffect(() => {
    props.products.forEach((product) => {
        if (quantities[product.id] === undefined) {
            quantities[product.id] = 1;
        }
    });
});

const isAuthenticated = computed(() => !!page.props.auth.user);
const flashMessage = computed(
    () => (page.props.flash as { success?: string } | undefined)?.success
);
const errors = computed(() => (page.props.errors ?? {}) as Record<string, string>);

const addToCart = (product: Product) => {
    const quantity = quantities[product.id] ?? 1;
    router.post(
        '/cart',
        { product_id: product.id, quantity },
        { preserveScroll: true }
    );
};

const formatPrice = (value: string | number) => {
    const amount = typeof value === 'string' ? Number(value) : value;
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">
                        Products
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Browse items and add them to your cart.
                    </p>
                </div>
            </div>

            <div
                v-if="flashMessage"
                class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
            >
                {{ flashMessage }}
            </div>

            <div
                v-if="errors.quantity || errors.product_id"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                {{ errors.quantity ?? errors.product_id }}
            </div>

            <div
                v-if="!isAuthenticated"
                class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700"
            >
                Log in to add items to your cart.
                <Link href="/login" class="ml-2 text-primary underline">
                    Log in
                </Link>
            </div>

            <div
                v-if="products.length"
                class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="rounded-xl border border-sidebar-border/70 bg-background p-4 shadow-sm"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="h-16 w-16 overflow-hidden rounded-lg border border-sidebar-border/70 bg-muted"
                            >
                                <img
                                    v-if="product.image_url"
                                    :src="product.image_url"
                                    :alt="product.name"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                />
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-foreground">
                                    {{ product.name }}
                                </h2>
                                <p class="text-sm text-muted-foreground">
                                    In stock: {{ product.stock_quantity }}
                                </p>
                            </div>
                        </div>
                        <p class="text-lg font-semibold text-foreground">
                            {{ formatPrice(product.price) }}
                        </p>
                    </div>

                    <div class="mt-4 flex items-center gap-2" v-if="isAuthenticated">
                        <Input
                            v-model.number="quantities[product.id]"
                            type="number"
                            min="1"
                            :max="product.stock_quantity"
                            class="w-24"
                            :disabled="product.stock_quantity < 1"
                        />
                        <Button
                            type="button"
                            :disabled="product.stock_quantity < 1"
                            @click="addToCart(product)"
                        >
                            Add to cart
                        </Button>
                    </div>
                    <div class="mt-4" v-else>
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="product.stock_quantity < 1"
                            @click="router.visit('/login')"
                        >
                            Log in to add
                        </Button>
                    </div>
                    <p
                        v-if="product.stock_quantity < 1"
                        class="mt-3 text-sm text-red-600"
                    >
                        Out of stock
                    </p>
                </div>
            </div>

            <div
                v-else
                class="rounded-xl border border-dashed border-sidebar-border/70 px-6 py-12 text-center text-sm text-muted-foreground"
            >
                No products available yet.
            </div>
        </div>
    </AppLayout>
</template>
