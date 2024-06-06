<style lang="scss" scoped>
.music-text {
  :deep() {
    span {
      @supports (-webkit-background-clip: text) or (background-clip: text) {
        background: linear-gradient(90deg, #1062af 0%, #7828be 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
      }
    }
  }
}
</style>

<template>
  <div h-full w-full of-hidden>
    <div h-full w-full flex="~ col" items-center justify-center>
      <div p-10 lt-md="w-[90%]" md="w-[60%]" flex="~ col" items-center justify-center gap-2 rounded-10
        :class="imageLoaded ? 'bg-[rgba(255,255,255,0.35)]' : 'bg-[rgba(0,0,0,0.35)]'">
        <div text="gray-6 lt-md:6 md:8">
          {{ playing ? '我正在听' : '当前没在听歌，最近听了' }}
        </div>
        <div lt-md="h-48 w-48" md="h-86 w-86" my-4 rounded-full of-hidden>
          <img v-show="imageLoaded" :src="music.image" h-full w-full object-cover :class="playing ? 'animate-spin animate-duration-30000' : ''
          " />
          <div v-show="!imageLoaded" i-ph-music-note-simple-duotone bg-gray-6 relative left-12 h="80%" w="80%"></div>
        </div>
        <Text v-if="visibleText" :class="{
          'music-text': imageLoaded
        }" text="gray-6 center lt-md:8 md:12" w-full h="lt-md:12 md:22" font-bold>{{ music.name }}</Text>
        <Text v-if="visibleText" :class="{
          'music-text': imageLoaded
        }" text="gray-6 center lt-md:4 md:6" w-full h="lt-md:8 md:10">{{ music.artist }}</Text>
      </div>
    </div>

    <div fixed top="-10%" left="-10%" h="120%" w="120%" z--1 blur-32>
      <img v-show="imageLoaded" :src="music.image" h-full w-full object-cover select-none />
      <div v-show="!imageLoaded" i-ph-music-note-simple-duotone bg-gray h-full w-full></div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, reactive, onMounted, nextTick, watch } from 'vue';

import { get } from 'lodash'
import axios from 'axios'

import Text from '@/components/Text.vue'

const playing = ref(false)
const imageLoaded = ref(false)

const visibleText = ref(true)

const music = reactive({
  name: '加载中',
  artist: '',
  image: ''
})

watch(music, () => {
  resetText()
})

onMounted(() => {
  getMusic()

  // 10 秒获取一次
  setInterval(() => {
    getMusic()
  }, 10000)
})

const getMusic = async () => {
  const response = await axios.get(import.meta.env.VITE_APP_API)

  if (!response || !response.data) {
    return
  }

  playing.value = get(response.data, '@attr.nowplaying') === 'true'

  music.name = get(response.data, 'name')
  music.artist = get(response.data, 'artist.#text')

  const imageUrl = get(response.data, 'albumCover', '')

  if (imageUrl && imageUrl !== music.image) {
    const image = new Image()
    image.src = imageUrl
    image.onload = () => {
      imageLoaded.value = true
      music.image = imageUrl
    }
  }
}

const resetText = () => {
  visibleText.value = false
  nextTick(() => {
    visibleText.value = true
  })
}
</script>
