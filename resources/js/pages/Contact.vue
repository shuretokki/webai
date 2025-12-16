<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import { Mail, MapPin, MessageSquare } from 'lucide-vue-next';

const container: any = {
  hidden: { opacity: 0 },
  show: {
    opacity: 1,
    transition: {
      staggerChildren: 0.1
    }
  }
};

const item: any = {
  hidden: { opacity: 0, y: 30 },
  show: {
    opacity: 1,
    y: 0,
    transition: {
      duration: 0.8,
      ease: [0.16, 1, 0.3, 1]
    }
  }
};
</script>

<template>
  <AppLayout>

    <Head title="Contact Us" />

    <div
      class="min-h-screen bg-white dark:bg-black text-black dark:text-white font-sans relative overflow-hidden pt-32 pb-24">

      <div class="absolute inset-0 z-0 pointer-events-none">
        <div
          class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-[0.03] dark:opacity-20">
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
        <Motion initial="hidden" animate="show" :variants="container"
          class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24">

          <Motion :variants="item" class="flex flex-col justify-center">
            <h1 class="text-5xl md:text-6xl font-bold tracking-tight mb-8">
              Let's start a <br />
              <span class="text-zinc-400">conversation.</span>
            </h1>
            <p class="text-xl text-zinc-600 dark:text-zinc-400 mb-12 max-w-lg leading-relaxed">
              Whether you have a question about features, pricing, or enterprise solutions, our team is ready to answer
              all your questions.
            </p>

            <div class="space-y-8">
              <div class="flex items-start gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-900 rounded-none border border-black/5 dark:border-white/5">
                  <Mail class="w-6 h-6" />
                </div>
                <div>
                  <h3 class="font-bold text-lg mb-1">Email Us</h3>
                  <p class="text-zinc-500">support@ecnelis.studio</p>
                </div>
              </div>

              <div class="flex items-start gap-4">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-900 rounded-none border border-black/5 dark:border-white/5">
                  <MapPin class="w-6 h-6" />
                </div>
                <div>
                  <h3 class="font-bold text-lg mb-1">Visit Us</h3>
                  <p class="text-zinc-500 max-w-xs">,<br />, </p>
                </div>
              </div>
            </div>
          </Motion>

          <Motion :variants="item">
            <div class="bg-zinc-50 dark:bg-zinc-900/50 p-8 md:p-12 border border-black/5 dark:border-white/5">
              <form class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-2">
                    <label class="text-sm font-medium">First Name</label>
                    <Input placeholder="Ryan"
                      class="h-12 bg-white dark:bg-black rounded-none border-black/10 dark:border-white/10 focus-visible:ring-1 focus-visible:ring-black dark:focus-visible:ring-white" />
                  </div>
                  <div class="space-y-2">
                    <label class="text-sm font-medium">Last Name</label>
                    <Input placeholder="Gosling"
                      class="h-12 bg-white dark:bg-black rounded-none border-black/10 dark:border-white/10 focus-visible:ring-1 focus-visible:ring-black dark:focus-visible:ring-white" />
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-medium">Email</label>
                  <Input type="email" placeholder="gosling@studio.com"
                    class="h-12 bg-white dark:bg-black rounded-none border-black/10 dark:border-white/10 focus-visible:ring-1 focus-visible:ring-black dark:focus-visible:ring-white" />
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-medium">Message</label>
                  <Textarea placeholder="Tell us about your project..."
                    class="min-h-[150px] bg-white dark:bg-black rounded-none border-black/10 dark:border-white/10 focus-visible:ring-1 focus-visible:ring-black dark:focus-visible:ring-white resize-none" />
                </div>

                <Button
                  class="w-full h-12 text-lg font-bold rounded-none bg-black hover:bg-zinc-800 text-white dark:bg-white dark:hover:bg-zinc-200 dark:text-black transition-colors">
                  Send Message
                </Button>
              </form>
            </div>
          </Motion>

        </Motion>
      </div>
    </div>
  </AppLayout>
</template>
