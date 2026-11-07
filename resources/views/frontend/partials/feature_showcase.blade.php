{{-- Category tabs and a horizontal row of feature cards. Content is limited to Meivoo routes and images already in the project. --}}
<section id="explore" class="mv-explore" data-explore aria-labelledby="explore-heading">
    <div class="container">
        <div class="mv-explore-intro">
            <span class="section-label">Inside the portal</span>
            <h2 id="explore-heading" class="section-title font-playfair mt-2">
                The family, <span class="text-gradient">kept together</span>
            </h2>
            <p class="section-lead mt-3">
                Open the tree, the messages, the gatherings, and the photos you already keep in Meivoo.
            </p>
        </div>

        <div class="mv-explore-toolbar">
            <div class="mv-explore-tabs" role="tablist" aria-label="Feature categories">
                <button type="button" class="mv-explore-tab" role="tab" id="explore-tab-all" aria-controls="explore-scroller" aria-selected="true" tabindex="0" data-category="all">All</button>
                <button type="button" class="mv-explore-tab" role="tab" id="explore-tab-tree" aria-controls="explore-scroller" aria-selected="false" tabindex="-1" data-category="tree">Tree</button>
                <button type="button" class="mv-explore-tab" role="tab" id="explore-tab-together" aria-controls="explore-scroller" aria-selected="false" tabindex="-1" data-category="together">Together</button>
                <button type="button" class="mv-explore-tab" role="tab" id="explore-tab-memories" aria-controls="explore-scroller" aria-selected="false" tabindex="-1" data-category="memories">Memories</button>
            </div>
            <button type="button" class="mv-explore-toggle" data-explore-toggle aria-pressed="false">
                <i class="fa-solid fa-pause" data-explore-toggle-icon aria-hidden="true"></i>
                <span class="visually-hidden" data-explore-toggle-label>Pause automatic slides</span>
            </button>
        </div>
        <p class="visually-hidden" id="explore-tab-help">Slides advance from the first card toward the next. Pause automatic slides with the pause button, or by hovering or focusing the carousel.</p>

        <div class="mv-explore-frame">
            <button type="button" class="mv-explore-nav mv-explore-nav-prev" data-explore-prev aria-label="Previous features">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </button>
            <button type="button" class="mv-explore-nav mv-explore-nav-next" data-explore-next aria-label="Next features">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </button>
            <div id="explore-scroller" class="mv-explore-viewport" data-explore-viewport role="tabpanel" aria-roledescription="carousel" aria-labelledby="explore-tab-all" aria-describedby="explore-tab-help">
                <div class="mv-explore-track" data-explore-track>
                    <article class="mv-feature-card" data-explore-card data-category="tree" style="--card-bg:#e7f0e4;">
                        <div class="mv-feature-copy">
                            <h3>Family tree</h3>
                            <p>Connect parents, spouses, and children, then follow the line across generations.</p>
                            <a href="{{ route('user.family-tree') }}">Open <span class="visually-hidden">family tree</span></a>
                        </div>
                        <div class="mv-feature-media">
                            <img src="{{ asset('assets/images/explore/family-tree.jpg') }}" alt="Grandmother holding her granddaughter outdoors" width="900" height="1125" loading="lazy" decoding="async" style="object-position:center 42%;">
                        </div>
                    </article>

                    <article class="mv-feature-card" data-explore-card data-category="tree" style="--card-bg:#f8f3ee;">
                        <div class="mv-feature-copy">
                            <h3>Family members</h3>
                            <p>A private directory of relatives, with a profile for each person in the family.</p>
                            <a href="{{ route('user.members') }}">Open <span class="visually-hidden">family members</span></a>
                        </div>
                        <div class="mv-feature-media">
                            <img src="{{ asset('assets/images/explore/family-members.jpg') }}" alt="Parents smiling with their son and baby" width="900" height="1125" loading="lazy" decoding="async" style="object-position:center 30%;">
                        </div>
                    </article>

                    <article class="mv-feature-card" data-explore-card data-category="together" style="--card-bg:#f3ece4;">
                        <div class="mv-feature-copy">
                            <h3>Messages</h3>
                            <p>Private messages for your family, with photos, files, and voice notes attached.</p>
                            <a href="{{ route('user.messages') }}">Open <span class="visually-hidden">messages</span></a>
                        </div>
                        <div class="mv-feature-media">
                            <img src="{{ asset('assets/images/explore/messages.jpg') }}" alt="Grandmother and granddaughter talking together" width="900" height="1125" loading="lazy" decoding="async" style="object-position:center 28%;">
                        </div>
                    </article>

                    <article class="mv-feature-card" data-explore-card data-category="together" style="--card-bg:#f7f3ea;">
                        <div class="mv-feature-copy">
                            <h3>Events</h3>
                            <p>Plan reunions, birthdays, and anniversaries, and let relatives say if they are coming.</p>
                            <a href="{{ route('user.events') }}">Open <span class="visually-hidden">events</span></a>
                        </div>
                        <div class="mv-feature-media">
                            <img src="{{ asset('assets/images/explore/events.jpg') }}" alt="Family gathered around a birthday cake" width="900" height="1125" loading="lazy" decoding="async" style="object-position:center 22%;">
                        </div>
                    </article>

                    <article class="mv-feature-card" data-explore-card data-category="memories" style="--card-bg:#f6f0e6;">
                        <div class="mv-feature-copy">
                            <h3>Legacy names</h3>
                            <p>Keep a name, what it means, who chose it, and the story behind it.</p>
                            <a href="{{ route('user.legacy-names') }}">Open <span class="visually-hidden">legacy names</span></a>
                        </div>
                        <div class="mv-feature-media">
                            <img src="{{ asset('assets/images/explore/legacy-names.jpg') }}" alt="Grandmother and granddaughter smiling at each other" width="900" height="1125" loading="lazy" decoding="async" style="object-position:center 35%;">
                        </div>
                    </article>

                    <article class="mv-feature-card" data-explore-card data-category="memories" style="--card-bg:#f8eef3;">
                        <div class="mv-feature-copy">
                            <h3>Photo restoration</h3>
                            <p>Restore, colorize, and clean up an old family photo, then keep the result.</p>
                            <a href="{{ route('user.ai-photo.index') }}">Open <span class="visually-hidden">photo restoration</span></a>
                        </div>
                        <div class="mv-feature-media">
                            <img src="{{ asset('assets/images/explore/photo-restoration.jpg') }}" alt="Hands sorting old family photographs" width="900" height="1125" loading="lazy" decoding="async" style="object-position:center 45%;">
                        </div>
                    </article>
                </div>
            </div>
        </div>
        <p class="visually-hidden" data-explore-live aria-live="polite"></p>
    </div>
</section>
