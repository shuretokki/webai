<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { ui } from '@/config/ui';
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">

        <Head title="Register" />

        <Form action="/register" method="post" :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email" :class="ui.auth.input.label">Email address</Label>
                    <Input id="email" type="email" required autofocus :tabindex="1" autocomplete="email" name="email"
                        placeholder="email@example.com" :class="ui.auth.input.base" />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" :class="ui.auth.input.label">Password</Label>
                    <Input id="password" type="password" required :tabindex="2" autocomplete="new-password"
                        name="password" placeholder="Password" :class="ui.auth.input.base" />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation" :class="ui.auth.input.label">Confirm password</Label>
                    <Input id="password_confirmation" type="password" required :tabindex="3" autocomplete="new-password"
                        name="password_confirmation" placeholder="Confirm password" :class="ui.auth.input.base" />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2" :class="ui.layout.pricingButton" tabindex="4" :disabled="processing"
                    data-test="register-user-button">
                    <Spinner v-if="processing" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-white/40">
                Already have an account?
                <TextLink :href="login()" class="text-white hover:underline underline-offset-4 ml-1" :tabindex="5">Log
                    in</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
