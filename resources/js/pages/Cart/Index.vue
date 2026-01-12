<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, reactive, watchEffect } from 'vue';

type CartProduct = {
    id: number;
    name: string;
    price: string;
    stock_quantity: number;
};

type CartItem = {
    id: number;
    quantity: number;
    product: CartProduct;
};

const props = defineProps<{
    items: CartItem[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Cart',
        href: '/cart',
    },
];

const page = usePage();
const quantities = reactive<Record<number, number>>({});

watchEffect(() => {
    props.items.forEach((item) => {
        quantities[item.id] = item.quantity;
    });
});

const isAuthenticated = computed(() => !!page.props.auth.user);
const flashMessage = computed(
    () => (page.props.flash as { success?: string } | undefined)?.success
);
const errors = computed(() => (page.props.errors ?? {}) as Record<string, string>);

const updateItem = (item: CartItem) => {
    const quantity = quantities[item.id] ?? item.quantity;
    router.put(
        `/cart/${item.id}`,
        { quantity },
        { preserveScroll: true }
    );
};

const removeItem = (item: CartItem) => {
    router.delete(`/cart/${item.id}`, { preserveScroll: true });
};

const checkout = () => {
    router.post('/cart/checkout', {}, { preserveScroll: true });
};

const total = computed(() =>
    props.items.reduce(
        (sum, item) => sum + item.quantity * Number(item.product.price),
        0
    )
);

const formatPrice = (value: string | number) => {
    const amount = typeof value === 'string' ? Number(value) : value;
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};
</script>

<template>
    <Head title="Cart" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">
                        Your cart
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Update quantities or remove items.
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
                v-if="errors.quantity || errors.cart"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                {{ errors.quantity ?? errors.cart }}
            </div>

            <div
                v-if="!isAuthenticated"
                class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700"
            >
                Log in to manage your cart and checkout.
                <Link href="/login" class="ml-2 text-primary underline">
                    Log in
                </Link>
            </div>

            <div
                v-if="items.length"
                class="rounded-xl border border-sidebar-border/70 bg-background p-4 shadow-sm"
            >
                <div
                    v-for="item in items"
                    :key="item.id"
                    class="flex flex-col gap-4 border-b border-sidebar-border/60 py-4 last:border-b-0"
                >
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <p class="text-lg font-semibold text-foreground">
                                {{ item.product.name }}
                            </p>
                            <p class="text-sm text-muted-foreground">
                                {{ formatPrice(item.product.price) }} each
                            </p>
                            <p class="text-sm text-muted-foreground">
                                In stock: {{ item.product.stock_quantity }}
                            </p>
                        </div>
                        <div class="text-right text-sm text-muted-foreground">
                            Line total
                            <div class="text-base font-semibold text-foreground">
                                {{
                                    formatPrice(
                                        item.quantity *
                                            Number(item.product.price)
                                    )
                                }}
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <Input
                            v-model.number="quantities[item.id]"
                            type="number"
                            min="1"
                            :max="item.product.stock_quantity"
                            class="w-24"
                        />
                        <Button
                            type="button"
                            variant="outline"
                            @click="updateItem(item)"
                        >
                            Update
                        </Button>
                        <Button
                            type="button"
                            variant="destructive"
                            @click="removeItem(item)"
                        >
                            Remove
                        </Button>
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center justify-between text-lg">
                        <span class="font-medium text-muted-foreground">Total</span>
                        <span class="font-semibold text-foreground">
                            {{ formatPrice(total) }}
                        </span>
                    </div>
                    <Button
                        v-if="isAuthenticated"
                        type="button"
                        @click="checkout"
                    >
                        Buy now
                    </Button>
                </div>
            </div>

            <div
                v-else
                class="rounded-xl border border-dashed border-sidebar-border/70 px-6 py-12 text-center text-sm text-muted-foreground"
            >
                <span v-if="isAuthenticated">Your cart is empty.</span>
                <span v-else>Log in to view your cart.</span>
                <Link
                    v-if="isAuthenticated"
                    href="/products"
                    class="ml-2 text-primary underline"
                >
                    Browse products
                </Link>
                <Link
                    v-else
                    href="/login"
                    class="ml-2 text-primary underline"
                >
                    Log in
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
