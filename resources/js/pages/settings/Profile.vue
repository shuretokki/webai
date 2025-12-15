<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';

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
const user = page.props.auth.user;
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

const fileInput = ref<HTMLInputElement | null>(null);
const uploading = ref(false);

const triggerFileInput = () => {
    fileInput.value?.click();
};

const handleAvatarChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        uploading.value = true;

        // Mock API call
        setTimeout(() => {
            uploading.value = false;
            alert('This is a mock upload. Backend implementation required.');
            // Ideally we would update user.avatar here with the response
        }, 1500);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="space-y-12">
                <!-- Profile Photo Section -->
                <div>
                    <h3 class="text-2xl font-normal text-foreground mb-2">Profile Photo</h3>
                    <p class="text-muted-foreground mb-6">Update your profile picture.</p>

                    <div class="flex items-center gap-6">
                        <Avatar class="h-24 w-24 border border-border">
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

                <!-- Name Section -->
                <div>
                    <h3 class="text-2xl font-normal text-foreground mb-2">My Profile</h3>
                    <p class="text-muted-foreground mb-6">Manage your public profile details.</p>

                    <Form v-bind="ProfileController.update.form()" class="max-w-xl"
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

                <!-- Email Section -->
                <div>
                    <h3 class="text-2xl font-normal text-foreground mb-2">Email</h3>
                    <p class="text-muted-foreground mb-6">Set or update the email address where you will receive
                        notifications.</p>

                    <Form v-bind="ProfileController.update.form()" class="max-w-xl"
                        v-slot="{ errors, processing, recentlySuccessful }">
                        <div class="grid gap-2">
                            <Label for="email" class="text-base font-medium">Email address</Label>
                            <div class="flex gap-4 items-start">
                                <div class="flex-1">
                                    <Input id="email" type="email"
                                        class="w-full bg-background border-input rounded-none h-10" name="email"
                                        :default-value="user.email" required autocomplete="username"
                                        placeholder="Email address" />
                                    <InputError class="mt-2" :message="errors.email" />
                                </div>
                                <Button :disabled="processing"
                                    class="rounded-none px-6 bg-foreground text-background hover:bg-foreground/90 h-10 shrink-0">
                                    Update email
                                </Button>
                            </div>

                            <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                <p v-show="recentlySuccessful" class="text-sm text-green-500 mt-2">Verified and saved.
                                </p>
                            </Transition>
                        </div>
                    </Form>

                    <div v-if="mustVerifyEmail && !user.email_verified_at"
                        class="mt-4 p-4 bg-yellow-500/10 border border-yellow-500/20 text-yellow-500">
                        <p class="text-sm">
                            Your email address is unverified.
                            <Link :href="send()" as="button" class="underline hover:no-underline font-medium ml-1">
                            Click here to resend verification.
                            </Link>
                        </p>
                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-xs font-bold">
                            Verification link sent!
                        </div>
                    </div>
                </div>

                <!-- Profile Other Details (Blue Box) -->
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
