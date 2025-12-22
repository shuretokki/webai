<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Motion } from 'motion-v';
import { ArrowRight } from 'lucide-vue-next';
import RevealFooter from '@/components/RevealFooter.vue';

interface Props {
  success?: string;
}

defineProps<Props>();

const form = useForm({
  name: '',
  email: '',
  company: '',
  message: '',
});

const submit = () => {
  form.post('/contact', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
    },
  });
};

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

const content = {
  footer: {
    links: {
      index: [
        { label: 'Works', href: '/works' },
        { label: 'Enterprise', href: '/enterprise' },
        { label: 'Docs', href: '/docs' },
      ],
      social: [
        { label: 'Twitter', href: '#' },
        { label: 'GitHub', href: '#' },
        { label: 'Discord', href: '#' },
      ]
    }
  }
};
</script>

<template>
  <AppLayout>

    <Head title="Contact" />

    <div class="min-h-screen bg-black text-white/90 selection:bg-white/10 pt-32 pb-24">
      <div class="max-w-[1200px] mx-auto px-6">
        <Motion initial="hidden" animate="show" :variants="container"
          class="grid grid-cols-1 lg:grid-cols-2 gap-24">

          <Motion :variants="item" class="flex flex-col">
            <h1 class="text-[clamp(2.5rem,8vw,5rem)] font-light tracking-tighter leading-[0.95] mb-12">
              Let's <br />
              <span class="text-white/30 italic">connect.</span>
            </h1>
            
            <div class="space-y-12 mt-auto pb-12">
              <div class="group">
                <span class="text-[11px] font-medium uppercase tracking-[0.3em] text-white/20 block mb-4">Email</span>
                <a href="mailto:hello@ecnelis.studio" class="text-2xl font-light hover:text-white transition-colors">
                  hello@ecnelis.studio
                </a>
              </div>

              <div class="group">
                <span class="text-[11px] font-medium uppercase tracking-[0.3em] text-white/20 block mb-4">Location</span>
                <p class="text-2xl font-light text-white/60">
                  Seoul, South Korea
                </p>
              </div>
            </div>
          </Motion>

          <Motion :variants="item">
            <div class="relative">
              <div v-if="success" 
                class="mb-12 p-6 border border-white/10 bg-white/5 text-white font-light tracking-tight">
                {{ success }}
              </div>

              <form @submit.prevent="submit" class="space-y-12">
                <div class="space-y-4 group">
                  <label class="text-[11px] font-medium uppercase tracking-[0.3em] text-white/20 group-focus-within:text-white/60 transition-colors">Name</label>
                  <input
                    v-model="form.name"
                    type="text"
                    placeholder="Your name"
                    class="w-full bg-transparent border-b border-white/10 py-4 text-xl font-light focus:outline-none focus:border-white transition-colors placeholder:text-white/10"
                  />
                  <p v-if="form.errors.name" class="text-xs text-red-500/60 font-mono uppercase tracking-widest">{{ form.errors.name }}</p>
                </div>

                <div class="space-y-4 group">
                  <label class="text-[11px] font-medium uppercase tracking-[0.3em] text-white/20 group-focus-within:text-white/60 transition-colors">Email</label>
                  <input
                    v-model="form.email"
                    type="email"
                    placeholder="name@example.com"
                    class="w-full bg-transparent border-b border-white/10 py-4 text-xl font-light focus:outline-none focus:border-white transition-colors placeholder:text-white/10"
                  />
                  <p v-if="form.errors.email" class="text-xs text-red-500/60 font-mono uppercase tracking-widest">{{ form.errors.email }}</p>
                </div>

                <div class="space-y-4 group">
                  <label class="text-[11px] font-medium uppercase tracking-[0.3em] text-white/20 group-focus-within:text-white/60 transition-colors">Message</label>
                  <textarea
                    v-model="form.message"
                    placeholder="Tell us about your project..."
                    class="w-full bg-transparent border-b border-white/10 py-4 text-xl font-light focus:outline-none focus:border-white transition-colors placeholder:text-white/10 min-h-[150px] resize-none"
                  ></textarea>
                  <p v-if="form.errors.message" class="text-xs text-red-500/60 font-mono uppercase tracking-widest">{{ form.errors.message }}</p>
                </div>

                <button
                  type="submit"
                  :disabled="form.processing"
                  class="group/btn inline-flex items-center gap-6 disabled:opacity-50">
                  <div class="px-8 py-4 bg-white text-black text-sm font-medium hover:bg-white transition-all group-hover/btn:px-10">
                    {{ form.processing ? 'Sending...' : 'Send Message' }}
                  </div>
                  <div class="size-14 border border-white/10 flex items-center justify-center group-hover/btn:border-white transition-colors">
                    <ArrowRight class="size-5" />
                  </div>
                </button>
              </form>
            </div>
          </Motion>

        </Motion>
      </div>
      <div class="h-[10dvh] bg-black relative z-20"></div>
      <RevealFooter :content="content" />
    </div>
  </AppLayout>
</template>
