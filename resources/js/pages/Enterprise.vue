<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Building2, CheckCircle2, Shield, Zap } from 'lucide-vue-next';

</script>

<template>
  <AppLayout>

    <Head title="Enterprise" />

    <div class="py-24 sm:py-32 bg-background relative overflow-hidden">
      <!-- Background Gradients -->
      <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute top-0 right-0 w-1/2 h-1/2 bg-primary/5 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-1/2 h-1/2 bg-blue-500/5 blur-[120px] rounded-full"></div>
      </div>

      <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-base font-semibold leading-7 text-primary font-space uppercase tracking-wide">Enterprise</h2>
          <p class="mt-2 text-4xl font-bold tracking-tight text-foreground sm:text-6xl font-space">Scale with confidence
          </p>
          <p class="mt-6 text-lg leading-8 text-muted-foreground">
            Custom solutions, dedicated support, and enterprise-grade security for large organizations.
          </p>
        </div>

        <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
          <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
            <div class="flex flex-col">
              <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-foreground font-space">
                <Shield class="h-5 w-5 text-primary" aria-hidden="true" />
                Advanced Security
              </dt>
              <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-muted-foreground">
                <p class="flex-auto">SSO enforcement, audit logs, and SOC2 compliance. Your data security is our top
                  priority.</p>
              </dd>
            </div>
            <div class="flex flex-col">
              <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-foreground font-space">
                <Building2 class="h-5 w-5 text-primary" aria-hidden="true" />
                Dedicated Infrastructure
              </dt>
              <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-muted-foreground">
                <p class="flex-auto">Private cloud deployment options and dedicated GPU clusters for maximum
                  performance.</p>
              </dd>
            </div>
            <div class="flex flex-col">
              <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-foreground font-space">
                <Zap class="h-5 w-5 text-primary" aria-hidden="true" />
                Custom Limits
              </dt>
              <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-muted-foreground">
                <p class="flex-auto">Need more tokens? Higher rate limits? We build custom plans tailored to your
                  specific workload.</p>
              </dd>
            </div>
          </dl>
        </div>

        <div class="mt-16 flex justify-center">
          <Button size="lg" class="text-lg px-8 rounded-full">
            Contact Sales
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
