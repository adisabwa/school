<template>
    <div id="psb" class="flex flex-col">
      <div class="flex flex-col items-center align-middle bg-white bg-opacity-[0.8]
        h-full py-16 mx-12 flex-1 text-center
        text-[20px]"> 
        <Pie v-if="loaded" :data="data" :options="options" class="h-[500px]" />
        <div>Total Calon Santri {{ total  }}</div>
      </div>
    </div>
  </template>
  
  <script>
  import { Chart as ChartJS, ArcElement, Title, Tooltip, Legend } from 'chart.js'
  import { Pie } from 'vue-chartjs'
  
  ChartJS.register(Title, ArcElement, Tooltip, Legend)
  
  export default {
    name: 'App',
    components: {
      Pie
    },
    computed: {
      total(){
        if (this.isEmpty(this.data.datasets)) return 0
        return this.data.datasets[0].data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
      }
    },  
    data() {
      return {
        loaded: false,
        data: {},
        options : {
          responsive: true,
          maintainAspectRatio: false,
          layout: {
            padding: {
              left: 20,
              right: 20,
              top: 20,
              bottom: 20,
            },
          },
          plugins: {
            legend: {
              position: 'top',
              labels: {
                font: {
                  size: 14,
                  family:'montserrat'
                },
              },
              title: {
                padding: {
                  bottom: 100,
                },
              },
            },
            title: {
              display: true,
              font: {
                size: 17,
                family:'montserrat'
              },
              text: `Data Pendaftaran Calon Santri`,
            },
            tooltip: {
              callbacks: {
                label: (tooltipItem) => {
                  const label = tooltipItem.label || '';
                  const value = tooltipItem.raw || 0;
                  const total = tooltipItem.dataset.data.reduce((a, b) => parseFloat(a) + parseFloat(b), 0);
                  const percentage = ((value / total) * 100).toFixed(2);
                  return `${label}: ${value} (${percentage}%)`;
                },
              },
              titleFont: {
                size: 13,
                family:'montserrat'
              },
              footerFont: {
                size: 12,
                family:'montserrat'
              },
            },
          }
        }  
      }
    },
    methods:{
      async getData(){
        this.loaded = false
        this.$http.get('/psb/admin/dashboard')
          .then(res => {
            let data = res.data
            this.data = data
            this.loaded = true
          })
          .catch(err => {
            console.log(err)
            this.$notify({
              type:'error',
              title: 'Gagal',
              message: 'Tidak dapat mengambil data',
              position: 'bottom-right',
            });
          })
      }
    },
    created(){
      this.getData()
    }
  }
  </script>
  