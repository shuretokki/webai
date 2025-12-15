<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { Github, Globe } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase title="Welcome back" description="Sign in to your account to continue">

        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="flex flex-col gap-6">
            <!-- Social Login -->
            <div class="grid grid-cols-2 gap-4">
                <a href="/auth/github/redirect"
                    class="flex items-center justify-center gap-2 px-4 py-2 border border-white/10 bg-white/5 hover:bg-white/10 hover:border-white/20 transition-all text-sm font-medium text-white rounded-none group">
                    <Github class="size-4 text-white/60 group-hover:text-white transition-colors" />
                    GitHub
                </a>
                <a href="/auth/google/redirect"
                    class="flex items-center justify-center gap-2 px-4 py-2 border border-white/10 bg-white/5 hover:bg-white/10 hover:border-white/20 transition-all text-sm font-medium text-white rounded-none group">
                    <Globe class="size-4 text-white/60 group-hover:text-white transition-colors" />
                    Google
                </a>
            </div>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t border-white/10" />
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-background px-2 text-muted-foreground">
                        Or continue with
                    </span>
                </div>
            </div>

            <Form v-bind="store.form()" :reset-on-success="['password']" v-slot="{ errors, processing }"
                class="flex flex-col gap-6">
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input id="email" type="email" name="email" required autofocus :tabindex="1"
                            autocomplete="email" placeholder="name@example.com"
                            class="rounded-none bg-white/5 border-white/10 focus-visible:ring-primary/20" />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center justify-between">
                            <Label for="password">Password</Label>
                            <TextLink v-if="canResetPassword" :href="request()"
                                class="text-xs text-muted-foreground hover:text-white transition-colors" :tabindex="5">
                                Forgot password?
                            </TextLink>
                        </div>
                        <Input id="password" type="password" name="password" required :tabindex="2"
                            autocomplete="current-password" placeholder="••••••••"
                            class="rounded-none bg-white/5 border-white/10 focus-visible:ring-primary/20" />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <Label for="remember" class="flex items-center space-x-3 cursor-pointer">
                            <Checkbox id="remember" name="remember" :tabindex="3"
                                class="rounded-none border-white/20 data-[state=checked]:bg-primary data-[state=checked]:border-primary" />
                            <span class="text-sm text-muted-foreground">Remember me</span>
                        </Label>
                    </div>

                    <Button type="submit" class="w-full rounded-none bg-white text-black hover:bg-white/90 font-medium"
                        :tabindex="4" :disabled="processing" data-test="login-button">
                        <Spinner v-if="processing" class="mr-2" />
                        Log in
                    </Button>
                </div>

                <div class="text-center text-sm text-muted-foreground" v-if="canRegister">
                    Don't have an account?
                    <TextLink :href="register()" :tabindex="5"
                        class="text-white hover:underline underline-offset-4 ml-1">Sign up</TextLink>
                </div>
            </Form>
        </div>
    </AuthBase>
</template>
