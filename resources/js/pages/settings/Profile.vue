<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import { SquarePen, Github, Globe } from 'lucide-vue-next';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

const page = usePage();
const user = computed(() => page.props.auth.user);
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { router } from '@inertiajs/vue3';

const fileInput = ref<HTMLInputElement | null>(null);
const uploading = ref(false);

const triggerFileInput = () => {
    fileInput.value?.click();
};

const handleAvatarChange = async (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const file = input.files[0];

        if (file.size > 800 * 1024) {
            alert('Image size must not exceed 800KB');
            return;
        }

        if (!['image/jpeg', 'image/jpg', 'image/png', 'image/gif'].includes(file.type)) {
            alert('Only JPG, PNG, and GIF files are allowed');
            return;
        }

        uploading.value = true;

        router.post('/api/user/avatar', {
            avatar: file
        }, {
            onFinish: () => {
                uploading.value = false;
                if (input) input.value = '';
            },
            onError: (errors) => {
                alert(Object.values(errors)[0] || 'Upload failed');
            }
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="space-y-12">
                <div>
                    <h3 class="text-2xl font-normal text-foreground mb-2">Profile Photo</h3>
                    <p class="text-muted-foreground mb-6">Update your profile picture.</p>

                    <div class="flex items-center gap-6">
                        <Avatar :key="user.avatar" class="h-24 w-24 border border-border">
                            <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                            <AvatarFallback class="text-lg">{{ user.name.charAt(0) }}</AvatarFallback>
                        </Avatar>

                        <div class="flex flex-col gap-2">
                            <input type="file" ref="fileInput" class="hidden" accept="image/*"
                                @change="handleAvatarChange" />
                            <Button variant="outline"
                                class="rounded-none border-input hover:bg-accent hover:text-accent-foreground"
                                :disabled="uploading" @click="triggerFileInput">
                                <span v-if="uploading">Uploading...</span>
                                <span v-else>Upload new picture</span>
                            </Button>
                            <p class="text-xs text-muted-foreground">JPG, GIF or PNG. Max size of 800K</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-2xl font-normal text-foreground mb-2">My Profile</h3>
                    <p class="text-muted-foreground mb-6">Manage your public profile details.</p>

                    <Form v-bind="ProfileController.update.form()" class="w-full"
                        v-slot="{ errors, processing, recentlySuccessful }">
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="name" class="text-base">Display Name</Label>
                                <div class="flex gap-4">
                                    <Input id="name" class="flex-1 bg-background border-input rounded-none h-10"
                                        name="name" :default-value="user.name" required autocomplete="name"
                                        placeholder="Full name" />
                                    <Button :disabled="processing" class="rounded-none px-6">
                                        Save
                                    </Button>
                                </div>

                                <InputError :message="errors.name" />
                                <p v-show="recentlySuccessful" class="text-sm text-green-500 mt-1">Saved.</p>
                            </div>
                        </div>
                    </Form>
                </div>

                <div>
                    <h3 class="text-2xl font-normal text-foreground mb-2">Email</h3>
                    <p class="text-muted-foreground mb-6">Update your email address securely with two-step verification.</p>

                    <div v-if="user.pending_email"
                        class="mb-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-none">
                        <p class="text-sm text-blue-600 dark:text-blue-500 mb-3">
                            <span class="font-medium">Email change pending.</span>
                            We sent a verification link to your current email (<span class="font-medium">{{ user.email
                            }}</span>).
                            Please check your inbox and click the link to continue.
                        </p>
                        <p class="text-xs text-muted-foreground mb-3">
                            New email will be: <span class="font-medium">{{ user.pending_email }}</span>
                        </p>
                        <Form action="/settings/profile/cancel-email-change" method="post"
                            v-slot="{ processing }">
                            <Button type="submit" variant="outline" :disabled="processing"
                                class="rounded-none text-xs h-8">
                                Cancel Email Change
                            </Button>
                        </Form>
                    </div>

                    <Form v-bind="ProfileController.update.form()" class="w-full"
                        v-slot="{ errors, processing, recentlySuccessful }">
                        <div class="grid gap-4">
                            <div class="grid gap-2">
                                <Label for="email" class="text-base font-medium">New email address</Label>
                                <Input id="email" type="email" class="w-full bg-background border-input rounded-none h-10"
                                    name="email" :default-value="user.email" required autocomplete="username"
                                    placeholder="Email address" :disabled="!!user.pending_email" />
                                <InputError class="mt-2" :message="errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="current_password" class="text-base font-medium">Current password</Label>
                                <Input id="current_password" type="password"
                                    class="w-full bg-background border-input rounded-none h-10" name="current_password"
                                    required autocomplete="current-password" placeholder="Enter your current password"
                                    :disabled="!!user.pending_email" />
                                <InputError class="mt-2" :message="errors.current_password" />
                                <p class="text-xs text-muted-foreground">Required to confirm your identity</p>
                            </div>

                            <div class="flex gap-4 items-center">
                                <Button :disabled="processing || !!user.pending_email"
                                    class="rounded-none px-6 bg-foreground text-background hover:bg-foreground/90 h-10">
                                    Request Email Change
                                </Button>
                                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                    <p v-if="recentlySuccessful || $page.props.status === 'verification-link-sent'"
                                        class="text-sm text-green-500">
                                        Verification email sent! Check your current email inbox.
                                    </p>
                                    <p v-else-if="$page.props.status === 'email-changed-verify-new'"
                                        class="text-sm text-green-500">
                                        Email updated! Please verify your new email address.
                                    </p>
                                    <p v-else-if="$page.props.status === 'email-change-cancelled'"
                                        class="text-sm text-muted-foreground">
                                        Email change cancelled.
                                    </p>
                                </Transition>
                            </div>
                        </div>
                    </Form>

                    <div v-if="mustVerifyEmail && !user.email_verified_at"
                        class="mt-6 p-4 bg-yellow-500/10 border border-yellow-500/20 rounded-none">
                        <p class="text-sm text-yellow-600 dark:text-yellow-500">
                            <span class="font-medium">Email verification required.</span>
                            Your email address is unverified. Please check your inbox or
                            <Link :href="send()" method="post" as="button"
                                class="underline hover:no-underline font-medium ml-1">
                            click here to resend the verification email
                            </Link>.
                        </p>
                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-xs font-bold">
                            Verification link sent!
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-2xl font-normal text-foreground mb-2">Connected Accounts</h3>
                    <p class="text-muted-foreground mb-6">Connect your social accounts to sign in faster.</p>

                    <div class="space-y-4 w-full">
                        <!-- GitHub -->
                        <div class="flex items-center justify-between p-4 border border-border bg-card/10 rounded-sm">
                            <div class="flex items-center gap-4">
                                <div class="p-2 bg-white/5 rounded-full border border-white/10">
                                    <Github class="size-6 text-foreground" />
                                </div>
                                <div>
                                    <h4 class="font-medium text-foreground">GitHub</h4>
                                    <p class="text-sm text-muted-foreground">Not connected</p>
                                </div>
                            </div>
                            <Button variant="outline" class="rounded-none border-input hover:bg-white/10" disabled>
                                Connect
                            </Button>
                        </div>

                        <div class="flex items-center justify-between p-4 border border-border bg-card/10 rounded-sm">
                            <div class="flex items-center gap-4">
                                <div class="p-2 bg-white/5 rounded-full border border-white/10">
                                    <Globe class="size-6 text-foreground" />
                                </div>
                                <div>
                                    <h4 class="font-medium text-foreground">Google</h4>
                                    <p class="text-sm text-muted-foreground">Not connected</p>
                                </div>
                            </div>
                            <Button variant="outline" class="rounded-none border-input hover:bg-white/10" disabled>
                                Connect
                            </Button>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-2xl font-normal text-foreground mb-6">Profile Details</h3>
                    <div class="bg-blue-900/10 border border-blue-500/30 p-6 rounded-none">
                        <div class="flex gap-4">
                            <SquarePen class="text-blue-400 text-xl mt-1 size-5" />
                            <div>
                                <h4 class="text-blue-400 font-medium mb-1">Update other details</h4>
                                <p class="text-blue-300/80 text-sm leading-relaxed">
                                    To change your profile picture or other social details, please visit your account
                                    dashboard.
                                    <br>Some details may be managed by your authentication provider (GitHub/Google).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-border">
                    <DeleteUser />
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
