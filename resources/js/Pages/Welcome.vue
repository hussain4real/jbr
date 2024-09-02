<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AOS from "aos";
import "aos/dist/aos.css";
import { onMounted, ref, toRefs } from "vue";
import FeaturedApartmentCards from "@/Components/FeaturedApartmentCards.vue";

const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    featuredApartments: {
        type: Array,
    },
});

// Access the props using toRefs
const { featuredApartments } = toRefs(props);
//filter by available status
const availableApartments = featuredApartments.value.filter(
    (apartment) => apartment.status === "available"
);

onMounted(() => {
    AOS.init();
});

function handleImageError() {
    document.getElementById("screenshot-container")?.classList.add("!hidden");
    document.getElementById("docs-card")?.classList.add("!row-span-1");
    document.getElementById("docs-card-content")?.classList.add("!flex-row");
    document.getElementById("background")?.classList.add("!hidden");
}
</script>

<template>
    <Head title="Welcome" />
    <div
        class="bg-gray-500 text-black/50 dark:bg-black dark:text-white/50 bg-fixed"
        style="
            background-image: linear-gradient(
                    rgba(0, 0, 0, 0.5),
                    rgba(0, 0, 0, 0.5)
                ),
                url('https://images.unsplash.com/photo-1707417091071-f790a4156994?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
        "
    >
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-0 lg:max-w-7xl">
                <header
                    class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3"
                >
                    <div class="flex lg:justify-start lg:col-start-1">
                        <svg
                            class="h-12 w-auto text-white lg:h-16 lg:text-[#FF2D20]"
                            viewBox="0 0 62 65"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0351V49.1337C49.9104 49.4902 49.7209 49.8192 49.4118 49.9987L25.4519 63.7916C25.3971 63.8227 25.3372 63.8427 25.2774 63.8639C25.255 63.8714 25.2338 63.8851 25.2101 63.8913C25.0426 63.9354 24.8666 63.9354 24.6991 63.8913C24.6716 63.8838 24.6467 63.8689 24.6205 63.8589C24.5657 63.8389 24.5084 63.8215 24.456 63.7916L0.501061 49.9987C0.348882 49.9113 0.222437 49.7853 0.134469 49.6334C0.0465019 49.4816 0.000120578 49.3092 0 49.1337L0 8.10652C0 8.01678 0.0124642 7.92953 0.0348998 7.84477C0.0423783 7.8161 0.0598282 7.78993 0.0697995 7.76126C0.0884958 7.70891 0.105946 7.65531 0.133367 7.6067C0.152063 7.5743 0.179485 7.54812 0.20192 7.51821C0.230588 7.47832 0.256763 7.43719 0.290416 7.40229C0.319084 7.37362 0.356476 7.35243 0.388883 7.32751C0.425029 7.29759 0.457436 7.26518 0.498568 7.2415L12.4779 0.345059C12.6296 0.257786 12.8015 0.211853 12.9765 0.211853C13.1515 0.211853 13.3234 0.257786 13.475 0.345059L25.4531 7.2415H25.4556C25.4955 7.26643 25.5292 7.29759 25.5653 7.32626C25.5977 7.35119 25.6339 7.37362 25.6625 7.40104C25.6974 7.43719 25.7224 7.47832 25.7523 7.51821C25.7735 7.54812 25.8021 7.5743 25.8196 7.6067C25.8483 7.65656 25.8645 7.70891 25.8844 7.76126C25.8944 7.78993 25.9118 7.8161 25.9193 7.84602C25.9423 7.93096 25.954 8.01853 25.9542 8.10652V33.7317L35.9355 27.9844V14.8846C35.9355 14.7973 35.948 14.7088 35.9704 14.6253C35.9792 14.5954 35.9954 14.5692 36.0053 14.5405C36.0253 14.4882 36.0427 14.4346 36.0702 14.386C36.0888 14.3536 36.1163 14.3274 36.1375 14.2975C36.1674 14.2576 36.1923 14.2165 36.2272 14.1816C36.2559 14.1529 36.292 14.1317 36.3244 14.1068C36.3618 14.0769 36.3942 14.0445 36.4341 14.0208L48.4147 7.12434C48.5663 7.03694 48.7383 6.99094 48.9133 6.99094C49.0883 6.99094 49.2602 7.03694 49.4118 7.12434L61.3899 14.0208C61.4323 14.0457 61.4647 14.0769 61.5021 14.1055C61.5333 14.1305 61.5694 14.1529 61.5981 14.1803C61.633 14.2165 61.6579 14.2576 61.6878 14.2975C61.7103 14.3274 61.7377 14.3536 61.7551 14.386C61.7838 14.4346 61.8 14.4882 61.8199 14.5405C61.8312 14.5692 61.8474 14.5954 61.8548 14.6253ZM59.893 27.9844V16.6121L55.7013 19.0252L49.9104 22.3593V33.7317L59.8942 27.9844H59.893ZM47.9149 48.5566V37.1768L42.2187 40.4299L25.953 49.7133V61.2003L47.9149 48.5566ZM1.99677 9.83281V48.5566L23.9562 61.199V49.7145L12.4841 43.2219L12.4804 43.2194L12.4754 43.2169C12.4368 43.1945 12.4044 43.1621 12.3682 43.1347C12.3371 43.1097 12.3009 43.0898 12.2735 43.0624L12.271 43.0586C12.2386 43.0275 12.2162 42.9888 12.1887 42.9539C12.1638 42.9203 12.1339 42.8916 12.114 42.8567L12.1127 42.853C12.0903 42.8156 12.0766 42.7707 12.0604 42.7283C12.0442 42.6909 12.023 42.656 12.013 42.6161C12.0005 42.5688 11.998 42.5177 11.9931 42.4691C11.9881 42.4317 11.9781 42.3943 11.9781 42.3569V15.5801L6.18848 12.2446L1.99677 9.83281ZM12.9777 2.36177L2.99764 8.10652L12.9752 13.8513L22.9541 8.10527L12.9752 2.36177H12.9777ZM18.1678 38.2138L23.9574 34.8809V9.83281L19.7657 12.2459L13.9749 15.5801V40.6281L18.1678 38.2138ZM48.9133 9.14105L38.9344 14.8858L48.9133 20.6305L58.8909 14.8846L48.9133 9.14105ZM47.9149 22.3593L42.124 19.0252L37.9323 16.6121V27.9844L43.7219 31.3174L47.9149 33.7317V22.3593ZM24.9533 47.987L39.59 39.631L46.9065 35.4555L36.9352 29.7145L25.4544 36.3242L14.9907 42.3482L24.9533 47.987Z"
                                fill="currentColor"
                            />
                        </svg>
                    </div>
                    <nav
                        v-if="canLogin"
                        class="flex justify-end lg:col-start-3"
                    >
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-2">
                    <div class="bg-cream">
                        <div
                            class="max-w-screen-xl px-8 mx-auto flex flex-col lg:flex-row items-start"
                        >
                            <!--Left Col-->
                            <div
                                class="flex flex-col w-full lg:w-6/12 justify-center lg:pt-24 items-start text-center lg:text-left mb-5 md:mb-0"
                            >
                                <h1
                                    data-aos="fade-right"
                                    data-aos-once="true"
                                    class="my-4 text-5xl font-bold leading-tight text-darken"
                                >
                                    <span class="text-orange-500"
                                        >Suburban</span
                                    >
                                    Luxury Resort
                                </h1>
                                <p
                                    data-aos="fade-down"
                                    data-aos-once="true"
                                    data-aos-delay="300"
                                    class="leading-normal text-2xl mb-8 text-slate-300"
                                >
                                    Situated in the heart of Wahiba Sands, we
                                    provide everything you need to enjoy a
                                    memorable stay.
                                </p>
                                <div
                                    data-aos="fade-up"
                                    data-aos-once="true"
                                    data-aos-delay="700"
                                    class="w-full md:flex items-center justify-center lg:justify-start md:space-x-5"
                                >
                                    <Link
                                        :href="route('apartments.index')"
                                        class="lg:mx-0 bg-orange-500 text-white text-xl font-bold rounded-full py-4 px-9 focus:outline-none transform transition hover:scale-110 duration-300 ease-in-out"
                                    >
                                        Book Now
                                    </Link>
                                    <div
                                        class="flex items-center justify-center space-x-3 mt-5 md:mt-0 focus:outline-none transform transition hover:scale-110 duration-300 ease-in-out"
                                    >
                                        <button
                                            class="bg-white w-14 h-14 rounded-full flex items-center justify-center"
                                        >
                                            <svg
                                                class="w-5 h-5 ml-2"
                                                viewBox="0 0 24 28"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M22.5751 12.8097C23.2212 13.1983 23.2212 14.135 22.5751 14.5236L1.51538 27.1891C0.848878 27.5899 5.91205e-07 27.1099 6.25202e-07 26.3321L1.73245e-06 1.00123C1.76645e-06 0.223477 0.848877 -0.256572 1.51538 0.14427L22.5751 12.8097Z"
                                                    fill="#23BDEE"
                                                />
                                            </svg>
                                        </button>
                                        <span
                                            class="cursor-pointer text-slate-100"
                                            >Watch how it works</span
                                        >
                                    </div>
                                </div>
                            </div>
                            <!--Right Col-->
                            <div
                                class="w-full lg:w-6/12 lg:-mt-10 relative"
                                id="girl"
                            >
                                <img
                                    data-aos="fade-up"
                                    data-aos-once="true"
                                    class="w-10/12 mx-auto 2xl:-mb-20"
                                    src="/assets/img/girl.png"
                                />
                                <!-- calendar -->
                                <!-- <div data-aos="fade-up" data-aos-delay="300" data-aos-once="true" class="absolute top-20 -left-6 sm:top-32 sm:left-10 md:top-40 md:left-16 lg:-left-0 lg:top-52 floating-4">
                                    <img class="bg-white bg-opacity-80 rounded-lg h-12 sm:h-16" src="/assets/img/ad_Artboard 1.svg">
                                </div> -->
                                <!-- red -->
                                <!-- <div data-aos="fade-up" data-aos-delay="400" data-aos-once="true" class="absolute top-20 right-10 sm:right-24 sm:top-28 md:top-36 md:right-32 lg:top-32 lg:right-16 floating">
                                    <img class="bg-white bg-opacity-80 rounded-lg h-12 sm:h-16" src="/assets/img/ad_Artboard 2.svg" alt="">
                                </div> -->
                                <!-- ux class -->
                                <!-- <div data-aos="fade-up" data-aos-delay="500" data-aos-once="true" class="absolute bottom-14 -left-4 sm:left-2 sm:bottom-20 lg:bottom-24 lg:-left-4 floating">
                                    <img class="bg-white bg-opacity-80 rounded-lg h-20 sm:h-28" src="/assets/img/ux-class.svg" alt="">
                                </div> -->
                                <!-- congrats -->
                                <!-- <div data-aos="fade-up" data-aos-delay="600" data-aos-once="true" class="absolute bottom-20 md:bottom-48 lg:bottom-52 -right-6 lg:right-8 floating-4">
                                    <img class="bg-white bg-opacity-80 rounded-lg h-12 sm:h-16" src="/assets/img/congrat.svg" alt="">
                                </div> -->
                            </div>
                        </div>
                        <div
                            class="text-white -mt-14 sm:-mt-24 lg:-mt-36 z-40 relative"
                        >
                            <svg
                                class="xl:h-40 xl:w-full"
                                data-name="Layer 1"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 1200 120"
                                preserveAspectRatio="none"
                            >
                                <path
                                    d="M600,112.77C268.63,112.77,0,65.52,0,7.23V120H1200V7.23C1200,65.52,931.37,112.77,600,112.77Z"
                                    fill="currentColor"
                                ></path>
                            </svg>
                            <div
                                class="bg-white w-full h-20 -mt-px text-slate-400"
                            >
                                <p
                                    class="text-center mx-auto container text-pretty text-xl"
                                >
                                    Since opening our doors, we have achieved a
                                    perfect balance between perfect luxury and
                                    simple Arabian nomadic simplicity in camps
                                    and tents and its convenience.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- container -->
                    <div
                        class="container px-4 lg:px-8 mx-auto max-w-screen-xl text-gray-700 overflow-x-hidden"
                    >
                        <!-- trusted by -->
                        <div class="max-w-4xl mx-auto">
                            <h1
                                class="text-center my-3 text-slate-400 font-medium"
                            >
                                Trusted by 5,000+ Companies Worldwide
                            </h1>
                            <div
                                class="grid grid-cols-3 lg:grid-cols-6 gap-4 justify-items-center"
                            >
                                <img
                                    class="h-7"
                                    src="/assets/img/company/google.svg"
                                />
                                <img
                                    class="h-7"
                                    src="/assets/img/company/netflix.svg"
                                />
                                <img
                                    class="h-7"
                                    src="/assets/img/company/airbnb.svg"
                                />
                                <img
                                    class="h-7 transform translate-y-2"
                                    src="/assets/img/company/amazon.svg"
                                />
                                <img
                                    class="h-7"
                                    src="/assets/img/company/facebook.svg"
                                />
                                <img
                                    class="h-7"
                                    src="/assets/img/company/grab.svg"
                                />
                            </div>
                        </div>

                        <!-- All-In-One Cloud Software. -->
                        <div
                            data-aos="flip-up"
                            class="max-w-xl mx-auto text-center mt-24"
                        >
                            <h1 class="font-bold text-darken my-3 text-2xl">
                                Experience Luxury at
                                <span class="text-orange-500"
                                    >Jawharat Bidiyah Resort.</span
                                >
                            </h1>
                            <p class="leading-relaxed text-slate-300 text-xl">
                                Discover unparalleled comfort, style, and a
                                range of amenities in our 54 luxurious
                                accommodations.
                            </p>
                        </div>
                        <!-- card -->
                        <div class="grid md:grid-cols-3 gap-14 md:gap-5 mt-20">
                            <div
                                data-aos="fade-up"
                                class="bg-white shadow-xl p-6 text-center rounded-xl"
                            >
                                <div
                                    style="background: #f48c06"
                                    class="rounded-full w-16 h-16 flex items-center justify-center mx-auto shadow-lg transform -translate-y-12"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-6 text-white"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75-1.5.75a3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0L3 16.5m15-3.379a48.474 48.474 0 0 0-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 0 1 6 13.12M12.265 3.11a.375.375 0 1 1-.53 0L12 2.845l.265.265Zm-3 0a.375.375 0 1 1-.53 0L9 2.845l.265.265Zm6 0a.375.375 0 1 1-.53 0L15 2.845l.265.265Z"
                                        />
                                    </svg>
                                </div>
                                <h1
                                    class="font-medium text-xl mb-3 lg:px-14 text-darken"
                                >
                                    Exquisite Dining & Restaurant
                                </h1>
                                <p class="px-4 text-gray-500">
                                    Savor a variety of delectable cuisines at
                                    our resort's restaurant, offering a blend of
                                    local and international flavors.
                                </p>
                            </div>
                            <div
                                data-aos="fade-up"
                                data-aos-delay="150"
                                class="bg-white shadow-xl p-6 text-center rounded-xl"
                            >
                                <div
                                    style="background: #f48c06"
                                    class="rounded-full w-16 h-16 flex items-center justify-center mx-auto shadow-lg transform -translate-y-12"
                                >
                                    <svg
                                        class="w-6 h-6 text-white"
                                        viewBox="0 0 48 48"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M12 0C11.0532 0 10.2857 0.767511 10.2857 1.71432V5.14285H13.7142V1.71432C13.7142 0.767511 12.9467 0 12 0Z"
                                            fill="#F5F5FC"
                                        />
                                        <path
                                            d="M36 0C35.0532 0 34.2856 0.767511 34.2856 1.71432V5.14285H37.7142V1.71432C37.7143 0.767511 36.9468 0 36 0Z"
                                            fill="#F5F5FC"
                                        />
                                        <path
                                            d="M42.8571 5.14282H37.7143V12C37.7143 12.9468 36.9468 13.7143 36 13.7143C35.0532 13.7143 34.2857 12.9468 34.2857 12V5.14282H13.7142V12C13.7142 12.9468 12.9467 13.7143 11.9999 13.7143C11.0531 13.7143 10.2856 12.9468 10.2856 12V5.14282H5.14285C2.30253 5.14282 0 7.44535 0 10.2857V42.8571C0 45.6974 2.30253 48 5.14285 48H42.8571C45.6975 48 48 45.6974 48 42.8571V10.2857C48 7.44535 45.6975 5.14282 42.8571 5.14282ZM44.5714 42.8571C44.5714 43.8039 43.8039 44.5714 42.857 44.5714H5.14285C4.19605 44.5714 3.42854 43.8039 3.42854 42.8571V20.5714H44.5714V42.8571Z"
                                            fill="#F5F5FC"
                                        />
                                        <path
                                            d="M13.7142 23.9999H10.2857C9.33889 23.9999 8.57138 24.7674 8.57138 25.7142C8.57138 26.661 9.33889 27.4285 10.2857 27.4285H13.7142C14.661 27.4285 15.4285 26.661 15.4285 25.7142C15.4285 24.7674 14.661 23.9999 13.7142 23.9999Z"
                                            fill="#F5F5FC"
                                        />
                                        <path
                                            d="M25.7143 23.9999H22.2857C21.3389 23.9999 20.5714 24.7674 20.5714 25.7142C20.5714 26.661 21.3389 27.4285 22.2857 27.4285H25.7143C26.6611 27.4285 27.4286 26.661 27.4286 25.7142C27.4286 24.7674 26.6611 23.9999 25.7143 23.9999Z"
                                            fill="#F5F5FC"
                                        />
                                        <path
                                            d="M37.7142 23.9999H34.2857C33.3389 23.9999 32.5714 24.7674 32.5714 25.7142C32.5714 26.661 33.3389 27.4285 34.2857 27.4285H37.7142C38.661 27.4285 39.4286 26.661 39.4286 25.7142C39.4286 24.7674 38.661 23.9999 37.7142 23.9999Z"
                                            fill="#F5F5FC"
                                        />
                                    </svg>
                                </div>
                                <h1
                                    class="font-medium text-xl mb-3 lg:px-14 text-darken"
                                >
                                    Events & Celebrations
                                </h1>
                                <p class="px-4 text-gray-500">
                                    Host your special occasions in style at our
                                    versatile event spaces, perfect for
                                    weddings, conferences, and more.
                                </p>
                            </div>
                            <div
                                data-aos="fade-up"
                                data-aos-delay="300"
                                class="bg-white shadow-xl p-6 text-center rounded-xl"
                            >
                                <div
                                    style="background: #f48c06"
                                    class="rounded-full w-16 h-16 flex items-center justify-center mx-auto shadow-lg transform -translate-y-12"
                                >
                                    <svg
                                        class="w-6 h-6 text-white"
                                        viewBox="0 0 60 60"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M30.0037 11.2377C30.4972 10.3661 31.7707 10.3661 32.2642 11.2377L37.9242 21.0487C38.18 21.489 38.6675 21.7581 39.1891 21.7581H49.9443C50.9307 21.7581 51.3593 22.9713 50.5862 23.5487L42.5173 29.5618C42.128 29.8479 41.9577 30.355 42.106 30.8273L44.9845 40.3433C45.2768 41.289 44.2181 42.0717 43.4565 41.4867L34.9007 35.0066C34.4956 34.7036 33.7723 34.7036 33.3672 35.0066L24.8113 41.4867C24.0497 42.0717 22.991 41.289 23.2833 40.3433L26.1618 30.8273C26.3101 30.355 26.1398 29.8479 25.7505 29.5618L17.6816 23.5487C16.9085 22.9713 17.3371 21.7581 18.3235 21.7581H29.0787C29.6003 21.7581 30.0878 21.489 30.3436 21.0487L30.0037 11.2377Z"
                                            fill="#F5F5FC"
                                        />
                                        <path
                                            d="M59.0112 26.3429C58.8525 25.7331 58.2463 25.3098 57.611 25.3098H50.6538C50.149 25.3098 49.6975 25.6283 49.4973 26.1007C49.2971 26.573 49.3885 27.1241 49.7192 27.5072L55.6427 34.4299H43.5385L41.1343 41.6995L48.7748 47.2117C49.1981 47.5134 49.7717 47.5786 50.2564 47.3806C50.7407 47.1827 51.0736 46.7588 51.1507 46.2612L54.0076 28.6227L59.201 33.6443C59.6224 34.0625 60.2969 34.0625 60.7183 33.6443L59.0112 26.3429ZM0 27.5146L2.8569 45.1531C2.934 45.6507 3.26695 46.0746 3.75127 46.2725C4.23486 46.4705 4.8085 46.4053 5.23182 46.1036L12.8723 40.5914L10.4682 33.3218H-1.63602L4.2874 26.3991C4.61806 26.016 4.70949 25.4649 4.50935 24.9926C4.30921 24.5203 3.85768 24.2018 3.35291 24.2018H-3.60429C-4.2396 24.2018 -4.84581 24.6251 -5.0046 25.235C-5.1634 25.8448 -4.86559 26.4907 -4.28486 26.8832L0 27.5146Z"
                                            fill="#F5F5FC"
                                        />
                                    </svg>
                                </div>
                                <h1
                                    class="font-medium text-xl mb-3 lg:px-14 text-darken"
                                >
                                    Outdoor Adventures
                                </h1>
                                <p class="px-4 text-gray-500">
                                    Explore the scenic desert landscape with
                                    thrilling activities like dune bashing,
                                    camel rides, and stargazing.
                                </p>
                            </div>
                        </div>

                        <!-- lorem -->
                        <div class="mt-28">
                            <div
                                data-aos="flip-down"
                                class="text-center max-w-screen-md mx-auto"
                            >
                                <h1 class="text-4xl font-bold mb-4 text-darken">
                                    Exceptional Services at
                                    <span class="text-orange-500"
                                        >Jawharat Bidiyah Resort</span
                                    >
                                </h1>
                                <p class="text-slate-300 text-xl">
                                    Book now and enjoy a hassle-free
                                    reservation. We offer a convenient online
                                    booking system that allows you to quickly
                                    and easily secure the perfect room for your
                                    stay. Our secure payment system ensures that
                                    your information is safe and your payment is
                                    processed quickly.
                                </p>
                            </div>
                            <div
                                data-aos="fade-up"
                                class="flex flex-col md:flex-row justify-center space-y-5 md:space-y-0 md:space-x-6 lg:space-x-10 mt-7"
                            >
                                <div class="relative md:w-5/12">
                                    <img
                                        class="rounded-2xl"
                                        src="/assets/img/Rectangle 19.png"
                                        alt=""
                                    />
                                    <div
                                        class="absolute bg-black bg-opacity-20 bottom-0 left-0 right-0 w-full h-full rounded-2xl"
                                    >
                                        <div
                                            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                                        >
                                            <h1
                                                class="uppercase text-white font-bold text-center text-sm lg:text-xl mb-3"
                                            >
                                                FOR INSTRUCTORS
                                            </h1>
                                            <button
                                                class="rounded-full text-white border text-xs lg:text-md px-6 py-3 w-full font-medium focus:outline-none transform transition hover:scale-110 duration-300 ease-in-out"
                                            >
                                                Start a class today
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative md:w-5/12">
                                    <img
                                        class="rounded-2xl"
                                        src="/assets/img/Rectangle 21.png"
                                        alt=""
                                    />
                                    <div
                                        class="absolute bg-black bg-opacity-20 bottom-0 left-0 right-0 w-full h-full rounded-2xl"
                                    >
                                        <div
                                            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                                        >
                                            <h1
                                                class="uppercase text-white font-bold text-center text-sm lg:text-xl mb-3"
                                            >
                                                FOR STUDENTS
                                            </h1>
                                            <button
                                                class="rounded-full text-white text-xs lg:text-md px-6 py-3 w-full font-medium focus:outline-none transform transition hover:scale-110 duration-300 ease-in-out"
                                                style="
                                                    background: rgba(
                                                        35,
                                                        189,
                                                        238,
                                                        0.9
                                                    );
                                                "
                                            >
                                                Enter access code
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sm:flex items-center sm:space-x-8 mt-36">
                            <div
                                data-aos="fade-right"
                                class="sm:w-1/2 relative"
                            >
                                <div
                                    class="bg-orange-500 rounded-full absolute w-12 h-12 z-0 -left-4 -top-3 animate-pulse"
                                ></div>
                                <h1
                                    class="font-semibold text-2xl relative z-50 text-darken lg:pr-10"
                                >
                                    Experience the wonders of the desert,
                                    <span class="text-orange-500"
                                        >right here at Jawharat Bidiyah
                                        Resort</span
                                    >
                                </h1>
                                <p class="py-5 lg:pr-32 text-xl text-slate-300">
                                    Our resort offers a unique blend of luxury
                                    and adventure. Explore the vast desert
                                    landscape, enjoy our world-class amenities,
                                    and unwind in our serene environment.
                                </p>
                                <a
                                    href="#"
                                    class="underline text-lg text-orange-500"
                                    >Discover More</a
                                >
                            </div>
                            <div
                                data-aos="fade-left"
                                class="sm:w-1/2 relative mt-10 sm:mt-0"
                            >
                                <div
                                    style="background: #23bdee"
                                    class="floating w-24 h-24 absolute rounded-lg z-0 -top-3 -left-3"
                                ></div>
                                <img
                                    class="rounded-xl z-40 relative"
                                    src="/assets/img/teacher-explaining.png"
                                    alt="Resort Experience"
                                />
                                <button
                                    class="bg-white w-14 h-14 rounded-full flex items-center justify-center absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 focus:outline-none transition hover:scale-110 duration-300 ease-in-out z-50"
                                >
                                    <svg
                                        class="w-5 h-5 ml-1"
                                        viewBox="0 0 24 28"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M22.5751 12.8097C23.2212 13.1983 23.2212 14.135 22.5751 14.5236L1.51538 27.1891C0.848878 27.5899 5.91205e-07 27.1099 6.25202e-07 26.3321L1.73245e-06 1.00123C1.76645e-06 0.223477 0.848877 -0.256572 1.51538 0.14427L22.5751 12.8097Z"
                                            fill="#23BDEE"
                                        />
                                    </svg>
                                </button>
                                <div
                                    class="bg-orange-500 w-40 h-40 floating absolute rounded-lg z-10 -bottom-3 -right-3"
                                ></div>
                            </div>
                        </div>

                        <div class="md:flex mt-40 md:space-x-10 items-start">
                            <div
                                data-aos="fade-down"
                                class="md:w-7/12 relative"
                            >
                                <div
                                    style="background: #33efa0"
                                    class="w-32 h-32 rounded-full absolute z-0 left-4 -top-12 animate-pulse"
                                ></div>
                                <div
                                    style="background: #33d9ef"
                                    class="w-5 h-5 rounded-full absolute z-0 left-36 -top-12 animate-ping"
                                ></div>
                                <img
                                    class="relative z-50 floating"
                                    src="/assets/img/vcall.png"
                                    alt="Desert Exploration"
                                />
                                <div
                                    style="background: #5b61eb"
                                    class="w-36 h-36 rounded-full absolute z-0 right-16 -bottom-1 animate-pulse"
                                ></div>
                                <div
                                    style="background: #f56666"
                                    class="w-5 h-5 rounded-full absolute z-0 right-52 bottom-1 animate-ping"
                                ></div>
                            </div>
                            <div
                                data-aos="fade-down"
                                class="md:w-5/12 mt-20 md:mt-0 text-slate-300"
                            >
                                <h1
                                    class="text-2xl font-semibold text-darken lg:pr-40"
                                >
                                    A
                                    <span class="text-orange-500"
                                        >luxury experience</span
                                    >
                                    designed for the desert
                                </h1>
                                <div class="flex items-center space-x-5 my-5">
                                    <div
                                        class="flex-shrink bg-white shadow-lg rounded-full p-3 flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            viewBox="0 0 27 26"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <rect
                                                width="11.8182"
                                                height="11.8182"
                                                rx="2"
                                                fill="#2F327D"
                                            />
                                            <rect
                                                y="14.1816"
                                                width="11.8182"
                                                height="11.8182"
                                                rx="2"
                                                fill="#2F327D"
                                            />
                                            <rect
                                                x="14.7727"
                                                width="11.8182"
                                                height="11.8182"
                                                rx="2"
                                                fill="#2F327D"
                                            />
                                            <rect
                                                x="14.7727"
                                                y="14.1816"
                                                width="11.8182"
                                                height="11.8182"
                                                rx="2"
                                                fill="#F48C06"
                                            />
                                        </svg>
                                    </div>
                                    <p class="">
                                        Explore the desert with guided tours and
                                        luxurious accommodations that make your
                                        stay unforgettable.
                                    </p>
                                </div>
                                <div class="flex items-center space-x-5 my-5">
                                    <div
                                        class="flex-shrink bg-white shadow-lg rounded-full p-3 flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            viewBox="0 0 28 26"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <rect
                                                x="8"
                                                y="6"
                                                width="20"
                                                height="20"
                                                rx="2"
                                                fill="#2F327D"
                                            />
                                            <rect
                                                width="21.2245"
                                                height="21.2245"
                                                rx="2"
                                                fill="#F48C06"
                                            />
                                        </svg>
                                    </div>
                                    <p>
                                        Enjoy our world-class amenities,
                                        including fine dining, spa services, and
                                        more.
                                    </p>
                                </div>
                                <div class="flex items-center space-x-5 my-5">
                                    <div
                                        class="flex-shrink bg-white shadow-lg rounded-full p-3 flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            viewBox="0 0 30 26"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M4.5 11.375C6.15469 11.375 7.5 9.91758 7.5 8.125C7.5 6.33242 6.15469 4.875 4.5 4.875C2.84531 4.875 1.5 6.33242 1.5 8.125C1.5 9.91758 2.84531 11.375 4.5 11.375ZM25.5 11.375C27.1547 11.375 28.5 9.91758 28.5 8.125C28.5 6.33242 27.1547 4.875 25.5 4.875C23.8453 4.875 22.5 6.33242 22.5 8.125C22.5 9.91758 23.8453 11.375 25.5 11.375ZM27 13H24C23.175 13 22.4297 13.3605 21.8859 13.9445C23.775 15.0668 25.1156 17.093 25.4062 19.5H28.5C29.3297 19.5 30 18.7738 30 17.875V16.25C30 14.4574 28.6547 13 27 13ZM15 13C17.9016 13 20.25 10.4559 20.25 7.3125C20.25 4.16914 17.9016 1.625 15 1.625C12.0984 1.625 9.75 4.16914 9.75 7.3125C9.75 10.4559 12.0984 13 15 13ZM18.6 14.625H18.2109C17.2359 15.1328 16.1531 15.4375 15 15.4375C13.8469 15.4375 12.7688 15.1328 11.7891 14.625H11.4C8.41875 14.625 6 17.2453 6 20.475V21.9375C6 23.2832 7.00781 24.375 8.25 24.375H21.75C22.9922 24.375 24 23.2832 24 21.9375V20.475C24 17.2453 21.5812 14.625 18.6 14.625ZM8.11406 13.9445C7.57031 13.3605 6.825 13 6 13H3C1.34531 13 0 14.4574 0 16.25V17.875C0 18.7738 0.670312 19.5 1.5 19.5H4.58906C4.88438 17.093 6.225 15.0668 8.11406 13.9445Z"
                                                fill="#2F327D"
                                            />
                                        </svg>
                                    </div>
                                    <p>
                                        Indulge in the beauty of the desert with
                                        every modern convenience at your
                                        fingertips.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Tools For Teachers And Learners -->
                        <div
                            class="flex flex-col md:flex-row items-center md:space-x-10 mt-16"
                        >
                            <div
                                data-aos="fade-right"
                                class="md:w-1/2 lg:pl-14"
                            >
                                <h1
                                    class="text-darken font-semibold text-3xl lg:pr-56"
                                >
                                    <span class="text-orange-500"
                                        >Experiences</span
                                    >
                                    Tailored for Every Guest
                                </h1>
                                <p class="text-slate-300 my-4 lg:pr-32">
                                    At Jawharat Bidiyah Resort, we offer a
                                    diverse range of experiences designed to
                                    cater to all our guests. Whether you're
                                    seeking adventure in the desert dunes or
                                    looking to unwind in luxurious comfort, our
                                    tailored experiences ensure an unforgettable
                                    stay.
                                </p>
                            </div>
                            <img
                                data-aos="fade-left"
                                class="md:w-1/2"
                                src="/assets/img/girl-with-books.png"
                                alt="Desert Adventure"
                            />
                        </div>

                        <!-- Assessments, Quizzes, Tests -->
                        <div
                            class="mt-20 flex flex-col-reverse md:flex-row items-center md:space-x-10"
                        >
                            <div data-aos="fade-right" class="md:w-6/12">
                                <img
                                    class="md:w-11/12"
                                    src="/assets/img/true-false.png"
                                    alt="Desert Camping"
                                />
                            </div>
                            <div
                                data-aos="fade-left"
                                class="md:w-6/12 md:transform md:-translate-y-20"
                            >
                                <h1
                                    class="font-semibold text-darken text-3xl lg:pr-64"
                                >
                                    Adventure Awaits,
                                    <span class="text-orange-500">Explore</span>
                                    the Desert
                                </h1>
                                <p class="text-slate-300 my-5 lg:pr-52">
                                    Embark on thrilling desert excursions, from
                                    dune bashing to stargazing. Your adventure
                                    begins right at our doorstep, with every
                                    detail taken care of.
                                </p>
                            </div>
                        </div>

                        <!-- Luxury Amenities for a Comfortable Stay -->
                        <div
                            class="flex flex-col md:flex-row items-center mt-12"
                        >
                            <div data-aos="fade-right" class="md:w-5/12">
                                <h1
                                    class="text-darken font-semibold text-3xl leading-tight lg:pr-32"
                                >
                                    <span class="text-orange-500">Luxury</span>
                                    Amenities for a Comfortable Stay
                                </h1>
                                <p class="my-5 lg:pr-14 text-slate-300">
                                    Experience the ultimate in comfort with our
                                    luxurious amenities, from spacious suites to
                                    exquisite dining. Every detail is designed
                                    to make your stay unforgettable.
                                </p>
                            </div>
                            <img
                                data-aos="fade-left"
                                class="md:w-7/12"
                                src="/assets/img/gradebook.png"
                                alt="Luxury Suite"
                            />
                        </div>

                        <!-- Personalized Guest Services -->
                        <div
                            class="mt-24 flex flex-col-reverse md:flex-row items-center md:space-x-10"
                        >
                            <div data-aos="fade-right" class="md:w-7/12">
                                <img
                                    class="md:w-11/12"
                                    src="/assets/img/discussion.png"
                                    alt="Personalized Service"
                                />
                            </div>
                            <div
                                data-aos="fade-left"
                                class="md:w-5/12 md:transform md:-translate-y-6"
                            >
                                <h1
                                    class="font-semibold text-darken text-3xl lg:pr-64"
                                >
                                    Personalized
                                    <span class="text-orange-500"
                                        >Guest Services</span
                                    >
                                </h1>
                                <p class="text-slate-300 my-5 lg:pr-24">
                                    Our team is dedicated to providing
                                    personalized services to meet your every
                                    need, ensuring that your stay is both
                                    relaxing and memorable.
                                </p>
                            </div>
                        </div>

                        <button
                            data-aos="flip-up"
                            class="px-5 py-3 border border-orange-500 text-orange-500 hover:text-slate-300 font-medium my-14 focus:outline-none transform transition hover:scale-110 duration-300 ease-in-out rounded-full mx-auto block hover:bg-orange-500"
                        >
                            See more features
                        </button>

                        <!-- INTEGRATIONS -->
                        <div
                            class="mt-24 flex flex-col md:flex-row items-start md:space-x-10"
                        >
                            <div data-aos="zoom-in-right" class="md:w-6/12">
                                <img
                                    class="md:w-8/12 mx-auto"
                                    src="/assets/img/integrations.png"
                                />
                            </div>
                            <div data-aos="zoom-in-left" class="md:w-6/12">
                                <div class="flex items-center space-x-20 mb-5">
                                    <span
                                        class="border border-gray-300 w-14 absolute"
                                    ></span>
                                    <h1
                                        class="text-gray-400 tracking-widest text-sm"
                                    >
                                        INTEGRATIONS
                                    </h1>
                                </div>
                                <h1
                                    class="font-semibold text-darken text-2xl lg:pr-40"
                                >
                                    200+ educational tools and platform
                                    <span class="text-orange-500"
                                        >integrations</span
                                    >
                                </h1>
                                <p class="text-slate-300 my-5 lg:pr-20">
                                    Schoology has every tool your classroom
                                    needs and comes pre-integrated with more
                                    than 200+ tools, student information systems
                                    (SIS), and education platforms.
                                </p>
                                <button
                                    class="px-5 py-3 border border-orange-500 text-orange-500 font-medium my-4 focus:outline-none transform transition hover:scale-110 duration-300 ease-in-out rounded-full hover:bg-orange-500 hover:text-slate-300"
                                >
                                    See All Integrations
                                </button>
                            </div>
                        </div>

                        <!-- TESTIMONIAL -->
                        <div
                            class="mt-24 flex flex-col-reverse md:flex-row items-start md:space-x-10"
                        >
                            <div data-aos="zoom-in-right" class="md:w-5/12">
                                <div class="flex items-center space-x-20 mb-5">
                                    <span
                                        class="border border-gray-300 w-14 absolute"
                                    ></span>
                                    <h1
                                        class="text-gray-400 tracking-widest text-sm"
                                    >
                                        TESTIMONIAL
                                    </h1>
                                </div>
                                <h1
                                    class="font-semibold text-darken text-2xl lg:pr-40"
                                >
                                    What They Say?
                                </h1>
                                <p class="text-slate-300 my-5 lg:pr-36">
                                    Skilline has got more than 100k positive
                                    ratings from our users around the world.
                                </p>
                                <p class="text-slate-300 my-5 lg:pr-36">
                                    Some of the students and teachers were
                                    greatly helped by the Skilline.
                                </p>
                                <p class="text-slate-300 my-5 lg:pr-36">
                                    Are you too? Please give your assessment
                                </p>
                                <button
                                    class="flex items-center space-x-3 pl-3 border-b border-l border-t border-orange-500 text-orange-500 font-medium my-4 focus:outline-none transform transition hover:scale-110 duration-300 ease-in-out rounded-full hover:bg-orange-500 hover:text-slate-300"
                                >
                                    <span>Write your assessment</span>
                                    <div
                                        class="border border-orange-500 h-14 w-14 rounded-full flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            viewBox="0 0 26 16"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M25.7071 8.70711C26.0976 8.31658 26.0976 7.68342 25.7071 7.2929L19.3431 0.928934C18.9526 0.538409 18.3195 0.538409 17.9289 0.928934C17.5384 1.31946 17.5384 1.95262 17.9289 2.34315L23.5858 8L17.9289 13.6569C17.5384 14.0474 17.5384 14.6805 17.9289 15.0711C18.3195 15.4616 18.9526 15.4616 19.3431 15.0711L25.7071 8.70711ZM-8.74228e-08 9L25 9L25 7L8.74228e-08 7L-8.74228e-08 9Z"
                                                fill="#F48C06"
                                            />
                                        </svg>
                                    </div>
                                </button>
                            </div>
                            <div data-aos="zoom-in-left" class="md:w-7/12">
                                <img
                                    class="md:w-10/12 mx-auto"
                                    src="/assets/img/testimonials.png"
                                />
                            </div>
                        </div>

                        <!-- featured apartments -->
                        <div class="container mx-auto" data-aos="zoom-in-left">
                            <h1
                                class="text-3xl text-center font-bold text-orange-500 my-8"
                            >
                                Featured Apartments
                            </h1>
                            <FeaturedApartmentCards
                                :apartments="availableApartments"
                                :horizontalScroll="true"
                            />
                        </div>

                        <!-- Latest News and Resources -->
                        <div data-aos="zoom-in" class="mt-16 text-center">
                            <h1 class="text-darken text-2xl font-semibold">
                                Latest News and Resources
                            </h1>
                            <p class="text-gray-500 my-5">
                                See the developments that have occurred to
                                Skillines in the world
                            </p>
                        </div>
                        <div
                            data-aos="zoom-in-up"
                            class="my-14 flex flex-col lg:flex-row lg:space-x-20"
                        >
                            <div class="lg:w-6/12">
                                <img
                                    class="w-full mb-6"
                                    src="/assets/img/laptop-news.png"
                                />
                                <span
                                    class="bg-yellow-300 text-darken font-semibold px-4 py-px text-sm rounded-full"
                                    >NEWS</span
                                >
                                <h1
                                    class="text-gray-800 font-semibold my-3 text-xl"
                                >
                                    Class adds $30 million to its balance sheet
                                    for a Zoom-friendly edtech solution
                                </h1>
                                <p class="text-gray-500 mb-3">
                                    Class, launched less than a year ago by
                                    Blackboard co-founder Michael Chasen,
                                    integrates exclusively...
                                </p>
                                <a href="" class="underline">Read more</a>
                            </div>
                            <div
                                class="lg:w-7/12 flex flex-col justify-between mt-12 space-y-5 lg:space-y-0 lg:mt-0"
                            >
                                <div class="flex space-x-5">
                                    <div class="w-4/12">
                                        <div class="relative">
                                            <img
                                                class="rounded-xl w-full"
                                                src="/assets/img/children-laptop.png"
                                            />
                                            <span
                                                class="absolute bottom-2 right-2 bg-yellow-300 text-darken font-semibold px-4 py-px text-sm rounded-full hidden sm:block"
                                                >PRESS RELEASE</span
                                            >
                                        </div>
                                    </div>
                                    <div class="w-8/12">
                                        <h1
                                            class="text-gray-800 text-sm sm:text-lg font-semibold"
                                        >
                                            Class Technologies Inc. Closes $30
                                            Million Series A Financing to Meet
                                            High Demand
                                        </h1>
                                        <p
                                            class="text-gray-500 my-2 sm:my-4 text-xs sm:text-md"
                                        >
                                            Class Technologies Inc., the company
                                            that created Class,...
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-5">
                                    <div class="w-4/12">
                                        <div class="relative">
                                            <img
                                                class="rounded-xl w-full"
                                                src="/assets/img/girl-laptop.png"
                                            />
                                            <span
                                                class="absolute bottom-2 right-2 bg-yellow-300 text-darken font-semibold px-4 py-px text-sm rounded-full hidden sm:block"
                                                >NEWS</span
                                            >
                                        </div>
                                    </div>
                                    <div class="w-8/12">
                                        <h1
                                            class="text-gray-800 text-sm sm:text-lg font-semibold"
                                        >
                                            Zoom’s earliest investors are
                                            betting millions on a better Zoom
                                            for schools
                                        </h1>
                                        <p
                                            class="text-gray-500 my-2 sm:my-4 text-xs sm:text-md"
                                        >
                                            Zoom was never created to be a
                                            consumer product. Nonetheless,
                                            the...
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-5">
                                    <div class="w-4/12">
                                        <div class="relative">
                                            <img
                                                class="rounded-xl w-full"
                                                src="/assets/img/cat-laptop.png"
                                            />
                                            <span
                                                class="absolute bottom-2 right-2 bg-yellow-300 text-darken font-semibold px-4 py-px text-sm rounded-full hidden sm:block"
                                                >NEWS</span
                                            >
                                        </div>
                                    </div>
                                    <div class="w-8/12">
                                        <h1
                                            class="text-gray-800 text-sm sm:text-lg font-semibold"
                                        >
                                            Former Blackboard CEO Raises $16M to
                                            Bring LMS Features to Zoom
                                            Classrooms
                                        </h1>
                                        <p
                                            class="text-gray-500 my-2 sm:my-4 text-xs sm:text-md"
                                        >
                                            This year, investors have reaped big
                                            financial returns from betting on
                                            Zoom...
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .container -->
                </main>

                <footer
                    class="mt-32"
                    style="background-color: rgba(37, 38, 65, 1)"
                >
                    <div
                        class="max-w-lg mx-auto flex container justify-center items-center gap-16"
                    >
                        <div
                            class="flex py-12 justify-center text-white items-center"
                        >
                            <div class="relative">
                                <h1
                                    class="font-bold text-xl pr-5 relative z-50"
                                >
                                    Skilline
                                </h1>
                                <svg
                                    class="w-11 h-11 absolute -top-2 -left-3 z-40"
                                    viewBox="0 0 79 79"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M35.9645 2.94975C37.9171 0.997129 41.0829 0.997127 43.0355 2.94975L76.0502 35.9645C78.0029 37.9171 78.0029 41.0829 76.0503 43.0355L43.0355 76.0502C41.0829 78.0029 37.9171 78.0029 35.9645 76.0503L2.94975 43.0355C0.997129 41.0829 0.997127 37.9171 2.94975 35.9645L35.9645 2.94975Z"
                                        stroke="#26C1F2"
                                        stroke-width="2"
                                    />
                                </svg>
                            </div>
                            <span
                                class="border-l border-gray-500 text-sm pl-5 py-2 font-semibold"
                                >Virtual Class for Zoom</span
                            >
                        </div>
                        <div class="text-center pb-16 pt-5">
                            <label class="text-gray-300 font-semibold"
                                >Subscribe to get our Newsletter</label
                            >
                            <div
                                class="px-5 sm:px-0 flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0 justify-center mt-3"
                            >
                                <input
                                    type="email"
                                    placeholder="Your Email"
                                    class="rounded-full py-2 pl-5 bg-transparent border border-gray-400"
                                />
                                <button
                                    type="submit"
                                    class="text-white w-40 sm:w-auto mx-auto sm:mx-0 font-semibold px-5 py-2 rounded-full"
                                    style="
                                        background: linear-gradient(
                                            105.5deg,
                                            #545ae7 19.57%,
                                            #393fcf 78.85%
                                        );
                                    "
                                >
                                    Subscribe
                                </button>
                            </div>
                        </div>
                        <div
                            class="flex items-center text-gray-400 text-sm justify-center"
                        >
                            <a href="" class="pr-3">Careers</a>
                            <a href="" class="border-l border-gray-400 px-3"
                                >Privacy</a
                            >
                            <a href="" class="border-l border-gray-400 pl-3"
                                >Terms & Conditions</a
                            >
                        </div>
                    </div>
                    <div class="text-center text-white py-3">
                        <p class="text-slate-400 text-sm">
                            &copy; 2021 Class Technologies Inc.
                        </p>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>
<style scoped>
/*primary color*/
.bg-cream {
    background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
        url("https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=2946&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D")
            no-repeat center center/cover;
            /* add top right border radius */
    border-top-right-radius: 1rem;
    border-top-left-radius: 1rem;
    /* make it a bit darker */
    /* background-color: rgba(37, 38, 65, 0.9); */
}

/*font*/
body {
    font-family: "Poppins", sans-serif;
}

/* .bg-orange-500 {
    background-color: #F48C06;
}
.text-orange-500 {
    color: #F48C06;
} */
.floating {
    animation-name: floating;
    animation-duration: 3s;
    animation-iteration-count: infinite;
    animation-timing-function: ease-in-out;
}
@keyframes floating {
    0% {
        transform: translate(0, 0px);
    }
    50% {
        transform: translate(0, 8px);
    }
    100% {
        transform: translate(0, -0px);
    }
}
.floating-4 {
    animation-name: floating;
    animation-duration: 4s;
    animation-iteration-count: infinite;
    animation-timing-function: ease-in-out;
}
@keyframes floating-4 {
    0% {
        transform: translate(0, 0px);
    }
    50% {
        transform: translate(0, 8px);
    }
    100% {
        transform: translate(0, -0px);
    }
}
.text-darken {
    color: #2f327d;
}
</style>
