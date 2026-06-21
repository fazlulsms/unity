@extends('layouts.public')
@section('title', 'Unity Circle — Friendship, Family & Community')

@section('content')

{{-- ══════════════════════════════════════════════════
     1. HERO
══════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white">
    {{-- Decorative blobs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -right-32 w-80 h-80 bg-indigo-500/15 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 w-64 h-64 bg-blue-400/10 rounded-full blur-2xl"></div>
    </div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-24 md:py-36 text-center">
        <div class="inline-flex items-center gap-2 bg-blue-600/20 ring-1 ring-blue-400/30 text-blue-300 text-xs font-semibold px-4 py-1.5 rounded-full mb-8">
            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full"></span>
            A Private Community of Trusted Professionals
        </div>

        <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold leading-tight tracking-tight mb-6">
            Building Lasting Friendships,<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">
                Strong Families,
            </span><br>
            and Shared Memories
        </h1>

        <p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto mb-10 leading-relaxed">
            Unity Circle is a close-knit community where professionals come together not for profit,
            but for the joy of genuine connection, mutual support, and lifelong friendship.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="#who-we-are"
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 ring-1 ring-white/20 text-white font-semibold px-6 py-3 rounded-xl transition-all duration-200">
                <i class="fas fa-circle-info text-sm"></i> Learn More
            </a>
            <a href="{{ route('apply') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-6 py-3 rounded-xl shadow-lg shadow-blue-900/40 transition-all duration-200">
                <i class="fas fa-user-plus text-sm"></i> Membership Application
            </a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-900 font-semibold px-6 py-3 rounded-xl shadow-md transition-all duration-200">
                <i class="fas fa-right-to-bracket text-sm text-blue-600"></i> Member Login
            </a>
        </div>

        {{-- Member count badge --}}
        @if($memberCount > 0)
        <div class="mt-12 flex items-center justify-center gap-3">
            <div class="flex -space-x-2">
                @foreach(['bg-blue-400','bg-indigo-400','bg-cyan-400','bg-violet-400'] as $c)
                <div class="w-9 h-9 rounded-full ring-2 ring-slate-900 {{ $c }} flex items-center justify-center">
                    <i class="fas fa-user text-white text-xs"></i>
                </div>
                @endforeach
            </div>
            <p class="text-slate-300 text-sm">
                <span class="text-white font-bold text-base">{{ $memberCount }}</span> active members and growing
            </p>
        </div>
        @endif
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     2. WHO WE ARE
══════════════════════════════════════════════════ --}}
<section id="who-we-are" class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-blue-600 text-sm font-bold uppercase tracking-widest">Who We Are</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 mb-5 leading-tight">
                    More Than a Club —<br>A Family of Friends
                </h2>
                <p class="text-gray-600 leading-relaxed mb-5 text-[15px]">
                    Unity Circle is a private, invitation-only community formed by a group of trusted professionals
                    who share a deep bond of friendship and mutual respect. We are not an investment platform,
                    savings group, or financial institution — we are a family.
                </p>
                <p class="text-gray-600 leading-relaxed mb-5 text-[15px]">
                    Founded on the belief that true wealth lies in human relationships, our community
                    brings together individuals from diverse professional backgrounds who are united by
                    shared values, genuine care for one another, and a commitment to growing together
                    — not just professionally, but as people.
                </p>
                <p class="text-gray-600 leading-relaxed text-[15px]">
                    Every gathering, every celebration, every moment of support we offer each other
                    strengthens the bonds that make Unity Circle what it truly is: a home away from home.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-handshake text-white text-lg"></i>
                    </div>
                    <p class="font-bold text-gray-900 text-sm">Built on Trust</p>
                    <p class="text-gray-500 text-xs mt-1">Every member is personally vouched for</p>
                </div>
                <div class="bg-indigo-50 rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-heart text-white text-lg"></i>
                    </div>
                    <p class="font-bold text-gray-900 text-sm">Built on Friendship</p>
                    <p class="text-gray-500 text-xs mt-1">Connections that last a lifetime</p>
                </div>
                <div class="bg-emerald-50 rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-people-roof text-white text-lg"></i>
                    </div>
                    <p class="font-bold text-gray-900 text-sm">Family Oriented</p>
                    <p class="text-gray-500 text-xs mt-1">We celebrate life's milestones together</p>
                </div>
                <div class="bg-amber-50 rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-star text-white text-lg"></i>
                    </div>
                    <p class="font-bold text-gray-900 text-sm">Professionals</p>
                    <p class="text-gray-500 text-xs mt-1">A community of accomplished individuals</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     3. OUR VISION
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <div class="w-14 h-14 bg-white/15 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-eye text-white text-2xl"></i>
        </div>
        <span class="text-blue-200 text-sm font-bold uppercase tracking-widest">Our Vision</span>
        <h2 class="text-3xl md:text-4xl font-extrabold mt-3 mb-6 leading-tight">
            A Community Where Every Member<br>Feels Truly at Home
        </h2>
        <p class="text-blue-100 text-lg leading-relaxed max-w-3xl mx-auto">
            We envision a lifelong community where friendship never fades, families are celebrated,
            and every member — regardless of life's changes — always has a circle of trusted companions
            to lean on. Unity Circle aspires to be the gold standard of what a private community can be:
            warm, purposeful, enduring, and deeply human.
        </p>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     4. OUR MISSION
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <span class="text-blue-600 text-sm font-bold uppercase tracking-widest">Our Mission</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 leading-tight">
                What We Set Out to Do Every Day
            </h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div class="flex gap-4 p-5 rounded-2xl bg-gray-50 hover:bg-blue-50 transition-colors duration-200">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-people-group text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Foster Genuine Friendship</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Create meaningful opportunities for members to connect, bond, and build deep personal friendships that transcend professional boundaries.</p>
                </div>
            </div>
            <div class="flex gap-4 p-5 rounded-2xl bg-gray-50 hover:bg-blue-50 transition-colors duration-200">
                <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-house-heart text-rose-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Celebrate Families Together</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Honor and include the families of our members in community life — welcoming spouses, children, and loved ones as part of our extended family.</p>
                </div>
            </div>
            <div class="flex gap-4 p-5 rounded-2xl bg-gray-50 hover:bg-blue-50 transition-colors duration-200">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-hands-holding-circle text-emerald-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Provide Mutual Support</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Stand together in times of joy and hardship alike, offering emotional, social, and practical support to every member of our community.</p>
                </div>
            </div>
            <div class="flex gap-4 p-5 rounded-2xl bg-gray-50 hover:bg-blue-50 transition-colors duration-200">
                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-calendar-star text-violet-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Create Shared Experiences</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Organize regular gatherings, events, and activities that create lasting memories and deepen the bonds between members and their families.</p>
                </div>
            </div>
            <div class="flex gap-4 p-5 rounded-2xl bg-gray-50 hover:bg-blue-50 transition-colors duration-200">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-shield-heart text-amber-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Uphold Values & Integrity</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Maintain a community culture grounded in trust, respect, and personal responsibility — ensuring Unity Circle remains a safe, positive space.</p>
                </div>
            </div>
            <div class="flex gap-4 p-5 rounded-2xl bg-gray-50 hover:bg-blue-50 transition-colors duration-200">
                <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-seedling text-teal-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Grow Together Long-Term</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Build a community that evolves with its members across life stages — celebrating milestones, adapting to needs, and staying connected for decades.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     5. OUR VALUES
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <span class="text-blue-600 text-sm font-bold uppercase tracking-widest">Our Values</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 leading-tight">
                The Principles That Define Us
            </h2>
            <p class="text-gray-500 mt-3 max-w-xl mx-auto text-[15px]">
                These six values are not aspirations — they are the living commitments that every
                Unity Circle member makes to one another.
            </p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                    <i class="fas fa-lock text-white text-lg"></i>
                </div>
                <h3 class="font-extrabold text-gray-900 text-lg mb-2">Trust</h3>
                <p class="text-gray-500 text-sm leading-relaxed">We keep our word. Every member is admitted on the basis of personal trust, and we work every day to honour that trust in how we act and speak within the community.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                    <i class="fas fa-heart text-white text-lg"></i>
                </div>
                <h3 class="font-extrabold text-gray-900 text-lg mb-2">Friendship</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Friendship is our foundation. We invest in relationships not because of what we gain, but because of the joy, richness, and meaning that genuine friendship brings to our lives.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                    <i class="fas fa-house-chimney-heart text-white text-lg"></i>
                </div>
                <h3 class="font-extrabold text-gray-900 text-lg mb-2">Family</h3>
                <p class="text-gray-500 text-sm leading-relaxed">We treat each other as family. The families of our members are welcomed into our circle, and we celebrate life's milestones — births, weddings, achievements — together.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                    <i class="fas fa-link text-white text-lg"></i>
                </div>
                <h3 class="font-extrabold text-gray-900 text-lg mb-2">Unity</h3>
                <p class="text-gray-500 text-sm leading-relaxed">We are stronger together. Unity is not about uniformity — it is about embracing our differences while remaining bound by shared values, mutual care, and collective purpose.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                    <i class="fas fa-hand-holding-heart text-white text-lg"></i>
                </div>
                <h3 class="font-extrabold text-gray-900 text-lg mb-2">Respect</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Every member deserves dignity. We listen before we speak, seek to understand before we judge, and treat every person in our community with genuine respect and warmth.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="w-12 h-12 bg-violet-600 rounded-2xl flex items-center justify-center mb-4 shadow-sm">
                    <i class="fas fa-scale-balanced text-white text-lg"></i>
                </div>
                <h3 class="font-extrabold text-gray-900 text-lg mb-2">Responsibility</h3>
                <p class="text-gray-500 text-sm leading-relaxed">We take ownership. Each member is responsible for contributing positively to our community — in attitude, in action, and in the way we uphold our shared commitments.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     6. WHY JOIN
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <span class="text-blue-600 text-sm font-bold uppercase tracking-widest">Why Join</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 leading-tight">
                What Membership Means to You
            </h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="relative bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 text-white overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-8 translate-x-8"></div>
                <div class="w-11 h-11 bg-white/15 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-circle-nodes text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">A Network That Cares</h3>
                <p class="text-blue-100 text-sm leading-relaxed">Join a circle of professionals who genuinely invest in each other's wellbeing — not networking for gain, but friendships for life.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <div class="w-11 h-11 bg-rose-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-days text-rose-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Regular Gatherings & Events</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Look forward to regular meetups, family outings, celebration events, and community activities that keep you connected year-round.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-hands-helping text-emerald-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Support in Every Season</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Whether you're celebrating a milestone or facing a challenge, your Unity Circle family stands with you — always present, always caring.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <div class="w-11 h-11 bg-violet-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-user-shield text-violet-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Privacy & Discretion</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Our community is intentionally private. Member information is kept strictly confidential and is never shared externally.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-trophy text-amber-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">A Community of Excellence</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Surround yourself with people who aspire to grow, contribute, and lead — both in their professions and in their personal lives.</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <div class="w-11 h-11 bg-teal-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-infinity text-teal-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-lg mb-2">Lifelong Belonging</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Unity Circle is designed to last. Membership grows and deepens over the years, creating bonds that span careers, families, and generations.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     7. COMMUNITY ACTIVITIES
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <span class="text-blue-600 text-sm font-bold uppercase tracking-widest">What We Do Together</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 leading-tight">
                Community Activities
            </h2>
            <p class="text-gray-500 mt-3 max-w-xl mx-auto text-[15px]">
                Life is richer when shared. Here are some of the ways Unity Circle members come together to create memories.
            </p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-utensils text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Social Gatherings</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Regular dinners, informal meetups, and festive celebrations that give members space to relax, laugh, and deepen friendships in a warm, comfortable setting.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-children text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Family Events</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Outings, picnics, and family-oriented celebrations that bring the whole family together — creating happy memories for members, spouses, and children alike.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-person-hiking text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Recreation & Leisure</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Sports events, travel trips, and recreational activities that encourage members to step away from work and enjoy life's simpler pleasures with good company.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 bg-violet-600 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-lightbulb text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Learning & Sharing</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Informal sessions where members share knowledge, professional experiences, and life lessons — enriching the community through collective wisdom.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-hands-holding-circle text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Community Projects</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Small-scale initiatives where members pool their talents and energy to do meaningful things together — strengthening bonds through shared effort and purpose.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-12 h-12 bg-teal-600 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-gift text-white text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Milestone Celebrations</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Birthdays, anniversaries, promotions, new arrivals — no member celebrates alone. Unity Circle makes sure every milestone is marked with love and community.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     8. MEMBERSHIP PRINCIPLES
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-blue-600 text-sm font-bold uppercase tracking-widest">Membership</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 mb-5 leading-tight">
                    How Membership Works
                </h2>
                <p class="text-gray-600 text-[15px] leading-relaxed mb-6">
                    Unity Circle membership is not open to the public. We are a private, invitation-based
                    community. Each new member joins because someone within the club personally vouches
                    for them — ensuring that every person who enters our circle is someone whose character,
                    integrity, and values align with who we are.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-user-check text-blue-600 text-xs"></i>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">Members are admitted by personal invitation and endorsement from existing members.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-gavel text-indigo-600 text-xs"></i>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">All applications are reviewed by the club leadership to ensure cultural fit and mutual trust.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-rotate text-emerald-600 text-xs"></i>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">Active participation in community activities is encouraged and valued.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-handshake-angle text-amber-600 text-xs"></i>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">Members are expected to contribute positively to the community's spirit and cohesion.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-shield text-violet-600 text-xs"></i>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">Member privacy and personal information is always protected and never shared externally.</p>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-comments text-teal-600 text-xs"></i>
                        </div>
                        <p class="text-gray-600 text-sm leading-relaxed">Open, respectful communication is the standard — we resolve differences with honesty and care.</p>
                    </li>
                </ul>
            </div>
            <div class="bg-gradient-to-br from-slate-900 to-blue-950 rounded-3xl p-8 text-white">
                <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="fas fa-door-open text-white text-2xl"></i>
                </div>
                <h3 class="text-2xl font-extrabold mb-3">Interested in Joining?</h3>
                <p class="text-slate-300 text-sm leading-relaxed mb-6">
                    If someone in Unity Circle has spoken to you about membership, we welcome your
                    application. Share a bit about yourself and why you'd like to be part of our
                    community — we look forward to getting to know you.
                </p>
                <div class="space-y-3">
                    <a href="{{ route('apply') }}"
                       class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-5 py-3 rounded-xl w-full transition-colors duration-200">
                        <i class="fas fa-user-plus"></i> Submit an Application
                    </a>
                    <a href="{{ route('contact') }}"
                       class="flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold px-5 py-3 rounded-xl w-full transition-colors duration-200">
                        <i class="fas fa-envelope"></i> Get in Touch First
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     9. TRANSPARENCY & GOVERNANCE
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <span class="text-blue-600 text-sm font-bold uppercase tracking-widest">Governance</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 leading-tight">
                Transparency & Community Governance
            </h2>
            <p class="text-gray-500 mt-3 max-w-2xl mx-auto text-[15px]">
                Unity Circle operates with openness, fairness, and clear accountability — so every member
                always knows how the community is run and feels confident in its leadership.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-sitemap text-blue-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Clear Leadership Structure</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Unity Circle is governed by a small elected leadership committee responsible for decisions, communication, and the overall health of the community.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-file-lines text-emerald-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Regular Member Updates</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Members receive regular updates on community activities, decisions, and plans through announcements and periodic meetings.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-11 h-11 bg-violet-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-comments text-violet-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Open Member Voice</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Every member has a voice. We hold open discussions and give all members an opportunity to contribute to important community decisions.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-book-open text-amber-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Written Rules & Constitution</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Our community operates according to a clear, written set of guidelines — ensuring consistency, fairness, and that every member is treated equally.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-11 h-11 bg-teal-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-rotate text-teal-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Annual Reviews</h3>
                <p class="text-gray-500 text-sm leading-relaxed">The community conducts annual reviews of its activities, policies, and leadership — reflecting on the past year and planning thoughtfully for the future.</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="w-11 h-11 bg-rose-100 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-lock text-rose-600 text-lg"></i>
                </div>
                <h3 class="font-bold text-gray-900 mb-2">Member Confidentiality</h3>
                <p class="text-gray-500 text-sm leading-relaxed">All member information and community discussions are kept strictly private. What happens in Unity Circle stays within Unity Circle — always.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     10. COMMUNITY POLICIES
══════════════════════════════════════════════════ --}}
<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12">
            <span class="text-blue-600 text-sm font-bold uppercase tracking-widest">Community Standards</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mt-2 leading-tight">
                Our Community Policies
            </h2>
            <p class="text-gray-500 mt-3 max-w-xl mx-auto text-[15px]">
                These policies exist to protect the warmth, safety, and integrity of our community for every member.
            </p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div class="flex gap-4 p-5 rounded-2xl border border-gray-100 bg-gray-50 hover:border-blue-100 hover:bg-blue-50/30 transition-colors duration-200">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-circle-check text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Respectful Communication</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">All members are expected to communicate with kindness, patience, and respect at all times — both in person and in digital spaces.</p>
                </div>
            </div>
            <div class="flex gap-4 p-5 rounded-2xl border border-gray-100 bg-gray-50 hover:border-blue-100 hover:bg-blue-50/30 transition-colors duration-200">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-user-secret text-indigo-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Privacy of Members</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Personal information shared within the community is strictly confidential. Members must never disclose another member's personal details without consent.</p>
                </div>
            </div>
            <div class="flex gap-4 p-5 rounded-2xl border border-gray-100 bg-gray-50 hover:border-blue-100 hover:bg-blue-50/30 transition-colors duration-200">
                <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-person-walking-arrow-right text-rose-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Active Participation</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Members are encouraged to engage with the community genuinely and regularly. Consistent absence without reason weakens the bonds we are here to build.</p>
                </div>
            </div>
            <div class="flex gap-4 p-5 rounded-2xl border border-gray-100 bg-gray-50 hover:border-blue-100 hover:bg-blue-50/30 transition-colors duration-200">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-ban text-amber-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Zero Tolerance for Disrespect</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Harassment, discrimination, bullying, or any form of disrespect has no place in Unity Circle. Violations are taken seriously by the leadership committee.</p>
                </div>
            </div>
            <div class="flex gap-4 p-5 rounded-2xl border border-gray-100 bg-gray-50 hover:border-blue-100 hover:bg-blue-50/30 transition-colors duration-200">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-clipboard-list text-emerald-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Commitment to Decisions</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Members agree to honour community decisions made through due process, even when personal views differ — demonstrating maturity and collective loyalty.</p>
                </div>
            </div>
            <div class="flex gap-4 p-5 rounded-2xl border border-gray-100 bg-gray-50 hover:border-blue-100 hover:bg-blue-50/30 transition-colors duration-200">
                <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fas fa-door-open text-violet-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm mb-1">Graceful Exit Policy</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">A member who chooses to leave does so respectfully and with proper notice. We part as friends, and the door always remains open for reconciliation.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     11. RECENT ANNOUNCEMENTS (conditional)
══════════════════════════════════════════════════ --}}
@if($notices->isNotEmpty())
<section class="py-20 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-blue-600 text-sm font-bold uppercase tracking-widest">Latest</span>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-1">Community Announcements</h2>
            </div>
            <a href="{{ route('notices') }}"
               class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1.5 group">
                View All <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
            </a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($notices as $notice)
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-600 text-xs font-semibold px-2.5 py-1 rounded-full">
                        <i class="fas fa-bullhorn text-xs"></i> Notice
                    </span>
                    <span class="text-gray-400 text-xs">{{ $notice->published_at?->format('d M Y') }}</span>
                </div>
                <h4 class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 mb-2">{{ $notice->title }}</h4>
                @if($notice->content)
                <p class="text-gray-500 text-xs leading-relaxed line-clamp-3">
                    {{ Str::limit(strip_tags($notice->content), 100) }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══════════════════════════════════════════════════
     12. MEMBERSHIP CTA
══════════════════════════════════════════════════ --}}
<section class="py-24 bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-blue-600/15 rounded-full blur-3xl"></div>
        <div class="absolute -top-16 right-1/4 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 text-center">
        <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-blue-900/40">
            <i class="fas fa-users text-white text-2xl"></i>
        </div>
        <h2 class="text-3xl md:text-5xl font-extrabold mb-5 leading-tight">
            Ready to Be Part of<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">
                Something Meaningful?
            </span>
        </h2>
        <p class="text-slate-300 text-lg mb-10 max-w-2xl mx-auto leading-relaxed">
            If you've been invited or are curious about Unity Circle, we'd love to hear from you.
            Membership is not just a status — it is a commitment to friendship, family, and building
            something genuinely special together.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('apply') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold px-8 py-4 rounded-xl shadow-lg shadow-blue-900/30 transition-all duration-200 text-base">
                <i class="fas fa-user-plus"></i> Apply for Membership
            </a>
            <a href="{{ route('contact') }}"
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 ring-1 ring-white/20 text-white font-bold px-8 py-4 rounded-xl transition-all duration-200 text-base">
                <i class="fas fa-envelope"></i> Contact Us
            </a>
        </div>
        <p class="mt-8 text-slate-500 text-sm">
            Already a member? <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-semibold underline underline-offset-2">Sign in to your portal</a>
        </p>
    </div>
</section>

@endsection
