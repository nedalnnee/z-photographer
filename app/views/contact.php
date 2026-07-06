<div class="max-w-3xl mx-auto py-10">
    <div class="text-center mb-12">
        <span class="px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase bg-rose-100 text-rose-600">Get in Touch</span>
        <h1 class="text-5xl font-black mt-4 tracking-tight">Contact <span class="text-rose-500">Us</span></h1>
        <p class="mt-4 text-base-content/60">Have a question or want to discuss a project? Reach out below.</p>
    </div>

    <div class="glass-card rounded-[3rem] overflow-hidden">
        <div class="p-10 lg:p-12">
            <form method="POST" action="?r=/contact/submit" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-rose-400">Your Name</span></label>
                        <input type="text" name="name" class="input input-bordered border-rose-100 rounded-2xl focus:border-rose-300" required>
                    </div>
                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-rose-400">Email Address</span></label>
                        <input type="email" name="email" class="input input-bordered border-rose-100 rounded-2xl focus:border-rose-300" required>
                    </div>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-rose-400">Subject</span></label>
                    <input type="text" name="subject" class="input input-bordered border-rose-100 rounded-2xl focus:border-rose-300">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-rose-400">Message</span></label>
                    <textarea name="message" class="textarea textarea-bordered border-rose-100 rounded-2xl h-40 focus:border-rose-300" required></textarea>
                </div>

                <button type="submit" class="btn border-none bg-gradient-to-r from-rose-400 to-pink-500 text-white w-full h-16 rounded-2xl text-lg font-bold shadow-lg shadow-rose-200 hover:scale-[1.01] transition-transform">
                    Send Message
                </button>
            </form>
        </div>
    </div>
</div>