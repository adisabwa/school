// Memuat Workbox secara lokal/offline-ready via CDN
importScripts('https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js');

if (workbox) {
  console.log('Workbox Berhasil Dimuat');

  // 1. Konfigurasi Dasar
  workbox.core.skipWaiting();
  workbox.core.clientsClaim();

  // 2. Precache file statis
  // Vite akan menyuntikkan manifes (array file) ke sini
  workbox.precaching.precacheAndRoute([{"revision":"f4d7eeea86e5facc8c2cd46848f97cd7","url":"/ppmda/assets/vue/registerSW.js"},{"revision":"475ac817556c181d825c2a45809aead2","url":"/ppmda/assets/vue/my-sw.js"},{"revision":"e1da1e8d4792fe077687a1e9273fa478","url":"/ppmda/assets/vue/files/___vite-browser-external_commonjs-proxy-DMiuQoDv.js"},{"revision":"5a895bceb2f325cd9d7ece57e45b84f9","url":"/ppmda/assets/vue/files/Winner-BUEFor-S.js"},{"revision":"0cc181dda6f357a09ac0cd3ecf4225dd","url":"/ppmda/assets/vue/files/ViewTable-OB5CQ03Z.js"},{"revision":"36bf25ff1fa87e787ddaa57a9ba27252","url":"/ppmda/assets/vue/files/View-CNq5U4Qk.css"},{"revision":"0ffee21dbf04e04124e29ce0ac32d55e","url":"/ppmda/assets/vue/files/View-BDKJ8bEZ.js"},{"revision":"cf92eea5057cddc26d4f09587d99e7bf","url":"/ppmda/assets/vue/files/vendor-xqhR7Dch.js"},{"revision":"d2d0656a6d142d1080f9d27961253b11","url":"/ppmda/assets/vue/files/vendor-BqRtHEZF.css"},{"revision":"590f098c768b36963064f4824c8da8f8","url":"/ppmda/assets/vue/files/useA2HS-Bp7nPAat.js"},{"revision":"929f7e562129af2f926f58651f040e02","url":"/ppmda/assets/vue/files/UploadDialog-JQybBUBa.js"},{"revision":"8af1c8ac773ff15db06da9ea3b3f0287","url":"/ppmda/assets/vue/files/UploadDialog-DYPa2sHp.css"},{"revision":"d61dda1a03b6ddec78c0a8f9c64034fa","url":"/ppmda/assets/vue/files/UnitList-UkxW-6gj.js"},{"revision":"981f39155a389b4c40224bbd63448d60","url":"/ppmda/assets/vue/files/Unauthorized-B6RQ2seV.js"},{"revision":"bec2f61d53dc7a9d00aa561b5c54f664","url":"/ppmda/assets/vue/files/Transaksi-LDtyacb0.js"},{"revision":"07128f755736437f93bf2c74339effbf","url":"/ppmda/assets/vue/files/TeacherScanner-DommZHXI.js"},{"revision":"39cf54da448bb8a9cb486b86f968a96e","url":"/ppmda/assets/vue/files/Tagihan-Bm84VY7H.js"},{"revision":"0d03b3076e687265080ac830fbe3ddb1","url":"/ppmda/assets/vue/files/TableFreeze-Cnr_uwi1.js"},{"revision":"209d7aecb649b9c84e939041cd2f9639","url":"/ppmda/assets/vue/files/TableData-j9CpFIIi.js"},{"revision":"f66059f7e97d59aab9a3d28cc8680754","url":"/ppmda/assets/vue/files/SummaryKedatangan-CZlYhkas.js"},{"revision":"ef4adf06758bd92a588e199362388f91","url":"/ppmda/assets/vue/files/Summary-CEMJRb90.js"},{"revision":"1711f39accb657d7ce418007fd240eb0","url":"/ppmda/assets/vue/files/Start-Dktf1r3g.js"},{"revision":"3c0fbe8311f33545a59c828a6015d666","url":"/ppmda/assets/vue/files/Start-boQ4uFwf.css"},{"revision":"952a66391c176704b736d188f0d35685","url":"/ppmda/assets/vue/files/SesiList-BX4EMHN-.js"},{"revision":"259e4b0814401104a8526b9aef0a961d","url":"/ppmda/assets/vue/files/SemesterList-CWjV12T6.js"},{"revision":"a3a0f0c336ecfed06ea1dd19d6e66665","url":"/ppmda/assets/vue/files/Sekretaris-BxqrEB2S.js"},{"revision":"1c11c4575b2284bcf198e2693a5a5b51","url":"/ppmda/assets/vue/files/Sekretaris-BafzK8Hw.css"},{"revision":"d8c4cc400b17e58496925a99f00033c3","url":"/ppmda/assets/vue/files/Scanner-DhsIuvg0.js"},{"revision":"6301051b4a2f2b02b0defb354be5707e","url":"/ppmda/assets/vue/files/Scanner-CJiQzemM.css"},{"revision":"0cd4ae7cc0a8bec556d3b2e94ea60a65","url":"/ppmda/assets/vue/files/SavedRpp-DbT0kfCA.js"},{"revision":"b960512c7b4ae8b49efb531c553f46e3","url":"/ppmda/assets/vue/files/SantriList-DJpMJOOK.js"},{"revision":"f655bae9994949e5e2bd69e9b1683b8d","url":"/ppmda/assets/vue/files/SantriKelas-A2IV1lhr.js"},{"revision":"50919ab2ccbf96b2a3bd43449e3aeb1e","url":"/ppmda/assets/vue/files/SantriKamar-CY0-POh3.js"},{"revision":"6053024e06910e1b10db292578f4ac90","url":"/ppmda/assets/vue/files/Saldo-DKchLYH_.js"},{"revision":"3df2b2efcccfde5b6f657c3a5cf41d26","url":"/ppmda/assets/vue/files/RppGenerator-CL7ST1c1.css"},{"revision":"aae993ac9fabb025309e7b924b7945cd","url":"/ppmda/assets/vue/files/RppGenerator-BJ7eaavV.js"},{"revision":"cca78db9a7a2f92810bdd5957255aa00","url":"/ppmda/assets/vue/files/ReportWalas-CSpyd1a8.js"},{"revision":"a47a4ccc66d9f366109cb133e91b82ad","url":"/ppmda/assets/vue/files/Report-fe2b693l.js"},{"revision":"cf2c5b475111917164c7c1feea892973","url":"/ppmda/assets/vue/files/Report-CjxWKHoW.js"},{"revision":"7b9b349bad54daee5dd9fd07709e5bd4","url":"/ppmda/assets/vue/files/Report-BihFZC1O.css"},{"revision":"4b6e66702a93308934f48ac1f446c8b4","url":"/ppmda/assets/vue/files/RekapSantri-fbKFu8Nv.js"},{"revision":"7ce471224b4b3f3bc0e652619c1b54ac","url":"/ppmda/assets/vue/files/RekapNilaiPengasuhan-BvORUNZU.js"},{"revision":"d4121c1e59cfa537862a2dd7f8bc41a0","url":"/ppmda/assets/vue/files/RekapNilaiAkhir-DzvF1hyr.js"},{"revision":"ddc8e06c3d09b557c81ccf406fb31803","url":"/ppmda/assets/vue/files/RekapNilai-NNaNt8cG.js"},{"revision":"30813d1b2c40dc98deb706e4ecac12d5","url":"/ppmda/assets/vue/files/Rekapitulasi-DCGG9t9y.css"},{"revision":"15a1cacc124adb6c7ff6fe5c0a21dfb2","url":"/ppmda/assets/vue/files/Rekapitulasi-Cnjo23Q_.js"},{"revision":"b87776791b9390d558aead8ab42355a9","url":"/ppmda/assets/vue/files/Register-D3YZschd.js"},{"revision":"453d37e6d769a3f32afcb03014ec8c5e","url":"/ppmda/assets/vue/files/QRScanner-BGnERIdN.js"},{"revision":"06c64d27abbe0ecd4c4dc997cbc47513","url":"/ppmda/assets/vue/files/PublicLayout-B2gJgTpx.js"},{"revision":"27250c0de953c52287733bf687bea010","url":"/ppmda/assets/vue/files/ProtaPromesView-CLuG5vqi.js"},{"revision":"be8320c66aee1cff0167f123ba5c800a","url":"/ppmda/assets/vue/files/ProgresNilai-CAGXoloq.js"},{"revision":"eaeb1ff9d0fc4f518a640993f93da6e5","url":"/ppmda/assets/vue/files/ProgresNilai-BLasb5ls.js"},{"revision":"b2d40b04605e16a98acc9d0d4f17f13e","url":"/ppmda/assets/vue/files/ProfilAnakOrangTua-xB4C5I0k.js"},{"revision":"1cbf3565428df7484bf791fb76f6c0d5","url":"/ppmda/assets/vue/files/ProfilAnakOrangTua-DGyyS5t3.css"},{"revision":"902b1e1e3e4c56c427b1a74e4547e72f","url":"/ppmda/assets/vue/files/ppmda-Bb5CeEeL.js"},{"revision":"51902910bd6b52f1b9c5673382443ce6","url":"/ppmda/assets/vue/files/postcss-VkSGpT8n.js"},{"revision":"4e780aa57b065f38c59b1ac6adc3e808","url":"/ppmda/assets/vue/files/Pos-Cdc_IRlx.js"},{"revision":"929605fb8170862948953b4799c4907c","url":"/ppmda/assets/vue/files/Penjadwalan-Doo8JQAc.js"},{"revision":"6fbba9d5d540a1980da290649d562de7","url":"/ppmda/assets/vue/files/PenghasilanList-BPBlfSdv.js"},{"revision":"1eec5f9d2be935d63639ef84dcc387a7","url":"/ppmda/assets/vue/files/PenggunaList-B47fA16G.js"},{"revision":"748f953d42c670fe37a60b86631f80d6","url":"/ppmda/assets/vue/files/Peminjaman-CKs922O1.js"},{"revision":"4e1471238c077d1c8eccd00e93b88422","url":"/ppmda/assets/vue/files/PembayaranTagihan-DtwXdVJM.js"},{"revision":"a52d092ba7c2b3cbd846fc6f36b0e3e6","url":"/ppmda/assets/vue/files/Pembagian-Bm_BUxlJ.js"},{"revision":"9e5599855630fdf673f485cff4d909e8","url":"/ppmda/assets/vue/files/PejabatList-BEPHty5e.js"},{"revision":"7cf9f283e6c2a3d09837d4e8cec356af","url":"/ppmda/assets/vue/files/Nilai-CuoV5Wbu.js"},{"revision":"7a78ecea36c029a15e59308c614eb279","url":"/ppmda/assets/vue/files/Nilai-Bv9gW9S0.js"},{"revision":"cd93790825b1aec28cb72a1a4ef7d785","url":"/ppmda/assets/vue/files/mihamsa-DG3ReJod.js"},{"revision":"a809c77036ca8bda5fd1651a7ea22114","url":"/ppmda/assets/vue/files/Metode-6DFYLRwM.js"},{"revision":"0504da6c85fe5a3d6ef4c473ffe26f0d","url":"/ppmda/assets/vue/files/menus-Y1-EpPSK.js"},{"revision":"0504da6c85fe5a3d6ef4c473ffe26f0d","url":"/ppmda/assets/vue/files/menus-QKUFYxUM.js"},{"revision":"a9529e0d55b154aa3279b32631e71379","url":"/ppmda/assets/vue/files/menus-DudWaLM-.js"},{"revision":"1600804144ecd9e9a6a19bb186e793ad","url":"/ppmda/assets/vue/files/menus-do-G22OB.js"},{"revision":"e9eb384de9cbd24df5182d2833529989","url":"/ppmda/assets/vue/files/menus-cS2vXTP1.js"},{"revision":"f7305f61bf32c85680f7a38a26682b00","url":"/ppmda/assets/vue/files/menus-CpXj9A_S.js"},{"revision":"64724b5b203c5ba0135bb83f5d7ddb79","url":"/ppmda/assets/vue/files/menus-CMd8r_wq.js"},{"revision":"108c9300019cf313183d44c1baae9043","url":"/ppmda/assets/vue/files/menus-b_PiwUK4.js"},{"revision":"dcf252e7ee531bafc1d3481ebbd78ea4","url":"/ppmda/assets/vue/files/menus-BSuWn_Ub.js"},{"revision":"21703751fc762e3c9d331bf4891e0c88","url":"/ppmda/assets/vue/files/menus-BAaVuL1g.js"},{"revision":"b31aa89ed32ba7a8e3f83b46f707b782","url":"/ppmda/assets/vue/files/menus-B7Zbl5BQ.js"},{"revision":"12e3f67cea2e9770a1771171f4595cac","url":"/ppmda/assets/vue/files/menus-B2syY2dR.js"},{"revision":"b89e302df8c57ce5fc311f740b4e9b39","url":"/ppmda/assets/vue/files/menus-B0SXJUa0.js"},{"revision":"0eef8ac3e94d2377cf3e4582998328b6","url":"/ppmda/assets/vue/files/Materi-u_uGErnK.js"},{"revision":"60595ca7bd0e1bd3d07bb9c82a7c50ac","url":"/ppmda/assets/vue/files/Materi-j1y7EcpM.css"},{"revision":"1c940ab30f7d8ac94cfde80b71f84d0c","url":"/ppmda/assets/vue/files/MatchSetting-BJKud0Fx.js"},{"revision":"82d916745597fa4edd76f41101acf6be","url":"/ppmda/assets/vue/files/main-BzVjuqhI.js"},{"revision":"7d14be906f75ab4c6d5b4a155b41e023","url":"/ppmda/assets/vue/files/main-BWtfCYDB.css"},{"revision":"6fc52df658059f1d8b98ca3b09db6dfd","url":"/ppmda/assets/vue/files/LowonganList-CR8j2MBB.js"},{"revision":"fb55158d72019f4c26a1df867893bacb","url":"/ppmda/assets/vue/files/LowonganForm-BG3-Nrsi.js"},{"revision":"6f4f4e15a9ccd09f143ed560edccd4ef","url":"/ppmda/assets/vue/files/LowonganDetail-DO_snvpF.js"},{"revision":"a02ffe018a328d46bcfb6ba9a7cdf955","url":"/ppmda/assets/vue/files/ListKehadiran-C648HOFQ.js"},{"revision":"a59cc35e08f87c84694a7805bb443976","url":"/ppmda/assets/vue/files/KelasList-DBP-JSKB.js"},{"revision":"e06cd3c14060f33658b38836f7d1ef65","url":"/ppmda/assets/vue/files/KelasAjarList-CGWaNGzH.js"},{"revision":"23844c87bef74ac90fec0de748932a43","url":"/ppmda/assets/vue/files/Kelas-DdtKwoeP.js"},{"revision":"9b61e6bb2a754706d66de735dca728b6","url":"/ppmda/assets/vue/files/Kelas-CBVd6ksk.css"},{"revision":"d91e7b5daa8d55033e7d8467cc890287","url":"/ppmda/assets/vue/files/Kategori-C_-XbJ3B.js"},{"revision":"2306ff19463623877598d8818922d460","url":"/ppmda/assets/vue/files/Kas-DoPUTJon.js"},{"revision":"bc9cfacb817156df3c5ebcdc409c6d11","url":"/ppmda/assets/vue/files/Kas-DiEiE0ao.js"},{"revision":"0b193885a37de73babb85dd494c7178d","url":"/ppmda/assets/vue/files/KamarList-DX2EhPwK.js"},{"revision":"696467f8431b019ae68a03cb775b09ec","url":"/ppmda/assets/vue/files/Kalender-SKGb3mCC.css"},{"revision":"b81aa863a22c6409247dca30240bcab1","url":"/ppmda/assets/vue/files/Kalender-B20lV-3_.js"},{"revision":"1e205209e7a36a1561ca9ca17bc8d1fb","url":"/ppmda/assets/vue/files/JurusanList-BIyXA5cP.js"},{"revision":"69a6c55d228e60c9c53490baa6d4f4c1","url":"/ppmda/assets/vue/files/JabatanList-_MKfZet9.js"},{"revision":"f0f19be3174702aa0a26849ab55a187f","url":"/ppmda/assets/vue/files/Izin-DKPQmJOi.js"},{"revision":"45265c5c1babfc24cb545abaccc77e6d","url":"/ppmda/assets/vue/files/Iuran-D3xsaHqf.js"},{"revision":"975a7552a71e3c49f98896213093b570","url":"/ppmda/assets/vue/files/Iuran-ai0KFcZa.js"},{"revision":"797f756557764df61c7ab0f14297fe22","url":"/ppmda/assets/vue/files/Info-DghAp9cJ.css"},{"revision":"3d3263de916e713d62f8a27d8f0a9c44","url":"/ppmda/assets/vue/files/Info-CearqSrB.js"},{"revision":"f734d78044736f136a472a35f5c52c9f","url":"/ppmda/assets/vue/files/Index-DnNcFOne.css"},{"revision":"041227e6e1b2bf6747ebada502dc5103","url":"/ppmda/assets/vue/files/Index-DMgpZ2n3.js"},{"revision":"3ee54756a2d68d15ba0612c2c9906365","url":"/ppmda/assets/vue/files/Index-CWGaK4pN.js"},{"revision":"5e2705d75dfd1dfd11c6e07377f3b9a9","url":"/ppmda/assets/vue/files/Index-Cv163HFT.js"},{"revision":"42c09f224380255fcbed9a2796e43b72","url":"/ppmda/assets/vue/files/Index-CRwjii-Y.js"},{"revision":"b05d4cabe91efeba32716742ff470df9","url":"/ppmda/assets/vue/files/Index-B7o3wHDj.js"},{"revision":"587c471fc6b501c6de7c3e2b58d1a196","url":"/ppmda/assets/vue/files/GuruList-CxfAE3lI.js"},{"revision":"204274aa3a5c087d89a73ce71b1445fa","url":"/ppmda/assets/vue/files/FormKehadiran-Dwy2sIbP.js"},{"revision":"8a7a50a90903328e4d6aa0f98ac3d6a6","url":"/ppmda/assets/vue/files/Form-DwCCtxB8.css"},{"revision":"ecdbcfc93d318651f179be0322a38788","url":"/ppmda/assets/vue/files/Form-Bw-EyzjX.js"},{"revision":"e50ed7e92487a8e00483971dc037f971","url":"/ppmda/assets/vue/files/Finish-C_BxG9cX.js"},{"revision":"69d828bd3ab2ac48b0507bf77b3e1789","url":"/ppmda/assets/vue/files/Finish-CAbyBzsU.js"},{"revision":"53ee340007c1d6aa922cbd94fcedcb95","url":"/ppmda/assets/vue/files/ExcelDialog-Ct79Wms1.js"},{"revision":"98e14d39024dd43f56b5a2e901c0892e","url":"/ppmda/assets/vue/files/el-upload-BJOGlY7_.css"},{"revision":"595b607e764ca1a64150e61d03203c4e","url":"/ppmda/assets/vue/files/el-tag-DljBBxJR.css"},{"revision":"fc4087dc89593c2f068d1e292b117839","url":"/ppmda/assets/vue/files/el-table-tbuj4A6J.css"},{"revision":"c8686a8182526a056302700f6034673f","url":"/ppmda/assets/vue/files/el-table-column-CEXH1TQP.css"},{"revision":"324ce6a889b29783ac00334db618a050","url":"/ppmda/assets/vue/files/el-switch-pqxnpAn2.css"},{"revision":"5638f0fa26a77f2027c0e6d8e01370d0","url":"/ppmda/assets/vue/files/el-step-CXC3BHrO.css"},{"revision":"df5ebfe2f91d8fad1eb6a37eb1f1f63c","url":"/ppmda/assets/vue/files/el-select-CnBhuJau.css"},{"revision":"a0e4189d3bc0104f854b83875b95f994","url":"/ppmda/assets/vue/files/el-scrollbar-BWxh-h6K.css"},{"revision":"c66ccb178ec6addca73e75154812eb2a","url":"/ppmda/assets/vue/files/el-row-C6BJsxyy.css"},{"revision":"3975706fd58c5414b392f3afdcbd3f9b","url":"/ppmda/assets/vue/files/el-progress-Dw9yTa91.css"},{"revision":"11dbe7e1783d780478088d5a740055d8","url":"/ppmda/assets/vue/files/el-pagination-BDwEpwR6.css"},{"revision":"814cbfa51634dca1d863b94fa4b06ed4","url":"/ppmda/assets/vue/files/el-loading-DLSpKYce.css"},{"revision":"91cd38e6bf41772c019c41c8f29ad0db","url":"/ppmda/assets/vue/files/el-input-number-BjNNn4iI.css"},{"revision":"68b329da9893e34099c7d8ad5cb9c940","url":"/ppmda/assets/vue/files/el-form-item-l0sNRNKZ.js"},{"revision":"3bcfa94a3c2d499ef4df45dadb263a6d","url":"/ppmda/assets/vue/files/el-form-CKZiX9BY.css"},{"revision":"642ff915c3a23ad28c6ffde4a7778a55","url":"/ppmda/assets/vue/files/el-dropdown-item-11ZCvSOX.css"},{"revision":"4fd9739f924e4c6c31849640771747d3","url":"/ppmda/assets/vue/files/el-divider-BUtF_RGI.css"},{"revision":"c47326b01c018a6537d74faffbfdd9d7","url":"/ppmda/assets/vue/files/el-date-picker-B9lHdXv9.css"},{"revision":"a8d73e0148fdae92878a5a388cae1668","url":"/ppmda/assets/vue/files/el-collapse-item-D7WIfuA2.css"},{"revision":"c0a90316e6c1b07628915506e5846797","url":"/ppmda/assets/vue/files/el-checkbox-RI4HkaMh.css"},{"revision":"a2afc8b9beea8a94124e9f7dfae12087","url":"/ppmda/assets/vue/files/el-card-fwQOLwdi.css"},{"revision":"a6bcfdd0e23c49153fb378c9b683845a","url":"/ppmda/assets/vue/files/el-alert-B9oGCRyi.css"},{"revision":"0053abb83eeb3e58733a1b9fe8c446a3","url":"/ppmda/assets/vue/files/DraggableTableData-DqneaOeT.js"},{"revision":"1c2705529ef7d1ad6f3e6c9bb0254272","url":"/ppmda/assets/vue/files/DraggableTableData-8v7YmXDu.css"},{"revision":"38f77751776bdc3f7057d0a4d858f998","url":"/ppmda/assets/vue/files/DownloadRapor-BREsD_wq.js"},{"revision":"d992cdb5f252c9c2b56dec120aa5dbd6","url":"/ppmda/assets/vue/files/DocumentEditor-DvIhn1cj.css"},{"revision":"bc50fcbd9c6deb923b25ede62fa9cdfc","url":"/ppmda/assets/vue/files/DocumentEditor-57xQW-l6.js"},{"revision":"b98f227f77edbe50ad51974769ef761a","url":"/ppmda/assets/vue/files/DetailTagihan-dBDNqaPV.js"},{"revision":"fe962129d3c6c5272ac063e316212c7b","url":"/ppmda/assets/vue/files/Default-CxXEQoPE.js"},{"revision":"07490b8c6694fef9cea7ae20a3031981","url":"/ppmda/assets/vue/files/DataView-BW7AlvUJ.js"},{"revision":"c2cc863397f0a3e0e608e55a425c914b","url":"/ppmda/assets/vue/files/DashboardNilai-srcT1CMS.js"},{"revision":"fb669212adef3db2946b9f3c0247be7a","url":"/ppmda/assets/vue/files/Dashboard-XMUZkDgw.js"},{"revision":"71ce729e0603683c64eb53bbd1f7d45c","url":"/ppmda/assets/vue/files/Dashboard-t_Lsm3zo.css"},{"revision":"aaa7af0c4c354db0207cb5ce09155792","url":"/ppmda/assets/vue/files/Dashboard-fSFGCda1.js"},{"revision":"d407c98b08d0bd90eaccd08a59ae87c4","url":"/ppmda/assets/vue/files/Dashboard-DxF86YmO.js"},{"revision":"349172d0555b4966098b8128f56bbd7a","url":"/ppmda/assets/vue/files/Dashboard-DNade4KV.js"},{"revision":"cc380d52b5b01fe23808bf5526f0c6b2","url":"/ppmda/assets/vue/files/Dashboard-DiT3tTfG.js"},{"revision":"a35a4032561084834ecdb6fb3d69d650","url":"/ppmda/assets/vue/files/Dashboard-DBxN2gwz.js"},{"revision":"b07b94cdfa56943899d613933a057e3c","url":"/ppmda/assets/vue/files/Dashboard-CZxsBtXt.js"},{"revision":"efa2cc401d8915d4cafc95b6f7f4e3c6","url":"/ppmda/assets/vue/files/Dashboard-CMJT-grg.css"},{"revision":"6970b4e8ee9c9f7936f4172e8b1c710e","url":"/ppmda/assets/vue/files/Dashboard-CMfH4PBG.css"},{"revision":"61da93293e49dd19a2d0cbc2f73b62ae","url":"/ppmda/assets/vue/files/Dashboard-ChNq59R9.js"},{"revision":"f3279612bbd3ddfb4967cf55dc228eaa","url":"/ppmda/assets/vue/files/Dashboard-CD0y1_v5.js"},{"revision":"08b22c98fa0da131a1131e47ed1fbbcd","url":"/ppmda/assets/vue/files/Dashboard-BUaErfQX.js"},{"revision":"837df30aefb81c679968546b14fa7ce0","url":"/ppmda/assets/vue/files/Dashboard-BmOw0p4M.js"},{"revision":"7516956a68026c1871c8a98e850f6f6f","url":"/ppmda/assets/vue/files/Dashboard-7QAwJtXY.js"},{"revision":"e82eddf0ec06765396d31cc7e7d7828f","url":"/ppmda/assets/vue/files/Dashboard-7P5igaYe.css"},{"revision":"500eb978ce9c973d5c466f15c065b5a3","url":"/ppmda/assets/vue/files/Dashboard-2S0FOXr9.js"},{"revision":"21ca08f52835d861a0990da17b24e4d4","url":"/ppmda/assets/vue/files/Dashboard-1wDNAqdI.js"},{"revision":"4920ee4cdb8ff9f45bf8a49f7c22850a","url":"/ppmda/assets/vue/files/Create-Cnv95qPB.js"},{"revision":"bd82129e7f7e9f9a11afa7c3beb21110","url":"/ppmda/assets/vue/files/CatatanWalas-BAf1TNpO.js"},{"revision":"b33fb055c11bf612cf9f28f19d751eec","url":"/ppmda/assets/vue/files/Buku-sJ-DhF4a.js"},{"revision":"b8652d19aaacd3a8e23283d8a80d452a","url":"/ppmda/assets/vue/files/BlankLayout-GxLiS1GK.js"},{"revision":"c173e03de53c1836d48e2dbffc134053","url":"/ppmda/assets/vue/files/Auth-CKm_yW0d.js"},{"revision":"f33f99c09dd750e403e340aaeb718bee","url":"/ppmda/assets/vue/files/Account-CcO7cBqJ.js"},{"revision":"7ed4d8880301d89afc3a3ab251e85800","url":"/ppmda/assets/vue/files/Account-C7ukmo_e.css"},{"revision":"b0d55b2b3e61e19d2a6e5b324ce15f51","url":"robots.txt"},{"revision":"579140b3c9e42eb39ae0e52b6129ae59","url":"manifest.webmanifest"}] || []);

  // 3. Runtime Caching
  // API Cache (Network First)
  // 3. Runtime Caching
  // API Cache (Network First)
  workbox.routing.registerRoute(
    ({ url }) => url.origin === 'https://api.example.com',
    new workbox.strategies.NetworkFirst({
      cacheName: 'api-cache'
    })
  );

  // Image Cache (Cache First)
  workbox.routing.registerRoute(
    /\.(?:png|jpg|jpeg|svg)$/,
    new workbox.strategies.CacheFirst({
      cacheName: 'image-cache',
      plugins: [
        new workbox.expiration.ExpirationPlugin({
          maxEntries: 50,
          maxAgeSeconds: 30 * 24 * 60 * 60, // 30 Hari
        }),
      ],
    })
  );

  // 4. LOGIKA PUSH API
  self.addEventListener('push', (event) => {
    let data = { title: 'Pemberitahuan', body: 'Ada pesan baru' };
    if (event.data) {
      try {
        data = event.data.json();
      } catch (e) {
        data.body = event.data.text();
      }
    }

    const options = {
      body: data.body,
      icon: '/ppmda/assets/images/icons/android-chrome-192x192.png',
      badge: '/ppmda/assets/images/icons/android-chrome-72x72.png',
      data: { url: data.url || '/' },
      // GETARAN LAMA (Pola: Getar 500ms, Diam 100ms, Getar 500ms, dst)
      // Ini akan terasa jauh lebih kuat daripada getaran standar
      vibrate: [1000, 110, 1000, 110, 1000, 110, 1000], 
      tag: 'urgent-alert-' + (data.id || ''),
      
      // RENOΤIFY: Paksa HP bergetar lagi meskipun notifikasi dengan tag yang sama sudah ada
      renotify: true,
      
      // REQUIRE INTERACTION: Notifikasi tidak akan hilang sampai user klik/swipe
      // Ini kunci agar terlihat seperti "Alarm"
      requireInteraction: true,
      priority: 'high',
      actions: [
        { action: 'open', title: 'LIHAT SEKARANG' },
        { action: 'close', title: 'ABAIKAN' },
        { action: 'coba', title: 'PERCOBAAN' },
      ],

    };

    event.waitUntil(
      self.registration.showNotification(data.title, options)
    );
  });

    // Klik Notifikasi
    self.addEventListener('notificationclick', (event) => {
    const notification = event.notification;
    const action = event.action; // Mengambil id 'open' atau 'close'

    // 1. Selalu tutup notifikasi setelah diklik
    notification.close();

    if (action === 'close') {
      // Jika tombol 'ABAIKAN' diklik, berhenti di sini (jangan buka link)
      console.log('User memilih untuk mengabaikan.');
      return;
    }

    // 2. Logika untuk tombol 'open' atau klik pada badan notifikasi
    const targetUrl = notification.data?.url || '/';

    event.waitUntil(
      clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
        // Jika tab aplikasi sudah terbuka, fokuskan saja
        for (let client of windowClients) {
          if (client.url.includes(targetUrl) && 'focus' in client) {
            return client.focus();
          }
        }
        // Jika belum terbuka, buka tab baru
        if (clients.openWindow) {
          return clients.openWindow(targetUrl);
        }
      })
    );
  });
} else {
  console.log('Workbox Gagal Dimuat');
}