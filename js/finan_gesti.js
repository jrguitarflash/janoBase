//load modulo financiero
window.onload=function()
{
	view=document.getElementById('finan_view').value;

	switch(view)
	{

		case 'finan_lstOpe':

			//load

				//periodos de operacion proyectos
				finan_periOpe_obte();

				//obtener estados de operacion de proyecto
				finan_estaOpe_obte();

				//obtener operaciones de proyecto
				setTimeout('finan_opeProye_obte()',1200);

			//eventos

					//evento direccionar nueva operacion proyecto

					$('#finan_opeNuev').click(function(mievento) // new
					{
						url="index.php";
						param="menu_id=162";
						param+="&menu=finan_frmOpe";
						gd_direPagParam(url,param);	
					});

					//evento filtrar operaciones por periodo

					$('#finan_periOpe').click(function(mievento)
					{
						finan_opeProye_obte();
					});

					//evento filtrar operaciones por estado

					$('#finan_estaOpe').click(function(mievento)
					{
						finan_opeProye_obte();
					});

					//evento aperturar operaciones del proyecto

					$('#finan_opeApe').click(function(mievento)
					{
						if(confirm("¿ Confirma aperturar operaciones seleccinadas ?"))
						{
							finan_opeProye_ope();
						}
					});

		break;

		case 'finan_frmOpe':


			//load

				$(function() 
				{
					$( "#tabs" ).tabs();
				});

				finan_opeProyexId_obte();
				setTimeout('finan_datCentxId()',1200);
				scc_dataComp_ini('finan_obteCentCost','finan_json','nc_ccId','nc_ccDes');

				/*-------------------------------------------*/
					// Modulo Finanzas & Centro Costos - Load
				/*-------------------------------------------*/

				setTimeout('finan_opeBanTem_obte()',1200);

				//------------------------o---------------------

				//iniciar adjuntos de operacion

				finan_adjuOpeXId_obte();

			//eventos

					//evento adjuntor documento de operacion

						$('#finan_adjuOpe').click(function(mievento)
						{
							//console.log("adjuntando documentos de operacion....!");
							finan_opeDoc_adju();
						});

					//evento volver a lista de operaciones

						$('#finan_opeVol').click(function(mievento)
						{
							url="index.php";
							param="menu_id=162";
							param+="&menu=finan_lstOpe";
							gd_direPagParam(url,param);	
						});

					//evento autocomplete centros de costos
					
						$('#nc_ccDes').keyup(function(mievento)
						{
							//console.log("iniciar datos de centro de costo...!");
							finan_datCentxId();		
						});

					//evento crear/actualizar operacion bancaria
					
						$('#finan_saveOpe').click(function(mievento)
						{
							finan_opeProye_crear();
						});
		
					//evento cerrar operacion de proyecto

						$("#finan_closeOpe").click(function()
						{
							//console.log("Cerrando operacion de proyecto...!");
							if(confirm("¿ Cerrar operaciones del proyecto ?"))
							{
								finan_opeProye_cerrar();
							}
						});


					//*****************************
					//Eventos Modulo Financiero
					//*****************************

					$('#finan_creadOpe').click(function(mievento) // new
					{
						console.log("Creando nueva operacion bancarioa temporal...!");

						if(document.getElementById('nc_ccId').value>0)
						{
							finan_openBanTem_cre();
						}
						else
						{
							alert("Generar Operacion del proyecto previamente...!");
						}

					});

					$('#finan_calComis').click(function(mievento) // new
					{
						console.log("Generando calculo de operaciones bancarias...!");
						finan_calcuReno('comisInte_calcu');
					});

					$('#finan_renoOpe').click(function(mievento) //new
					{
						finan_calcuReno('opeBan_reno');
					});

					$('#finan_cargAlerta').click(function(mievento) //new
					{
						location.href="cronjob/finan_cronjob.php?cron=prevAlert";
					});
 
					//-------------------o--------------------

		break;

		default:
		break;

	}

}



//finan_lstOpe

			//funciones

				//JS

					function finan_dirEdit(id)
					{
						//test
						//console.log("test evento");

						url="index.php";
						param="menu_id=162";
						param+="&menu=finan_frmOpe";
						param+="&id="+id;
						gd_direPagParam(url,param);
					}

				//JSON

					function finan_opeProye_ope()
					{
						/*vars*/
						json="finan_opeProye_ope";
						opeId=new Array();
						insFrm=document.finan_opeProye_frm.finan_opeProyeId;
						opeId=gd_checkData(insFrm);
						console.log(opeId);

						/*peticion json*/
						$.ajax({
					    type:"POST",
					    url: 'json/finan_json.php',
					    data:{json:json,opeId:opeId},
					    dataType: 'json',
					    success: function(data) 
					    {
					    	//console.log(data[0]);
					    	if(data[0]>0)
					    	{
					    		$(".elem-gd").notify(data[0]+" operacion de proyectos aperturadas correctamente","success");

					    		//obtener operaciones de proyecto
								setTimeout('finan_opeProye_obte()',1200);
					    	}
					    	else
					    	{
					    		$(".elem-gd").notify("operacion de proyectos no aperturadas","error");
					    	}
						}

					    });

					}

				//AJAX

					function finan_opeProye_obte()
					{

						//vars
						ajax="finan_opeProye_obte";
						periOpe=kd_obteValComb('finan_periOpe');
						estaOpe=kd_obteValComb('finan_estaOpe');

						//console.log(periOpe);
						//console.log(estaOpe);

						//peticion ajax
						var request = $.ajax(
						{
							url: "ajax/finan_ajax.php",
							type: "POST",
							data: {ajax:ajax,periOpe:periOpe,estaOpe:estaOpe},
							dataType: "html"
						});
						
						request.done(function(msg) 
						{
							//console.log(msg);
							$("#finan_opeProye").html(msg);
						});
						
						request.fail(function(jqXHR, textStatus) 
						{
							alert( "Request failed: " + textStatus );
						});

					}

					function finan_periOpe_obte()
					{
						//vars
						ajax="finan_periOpe_obte";

						//peticion ajax
						var request = $.ajax(
						{
							url: "ajax/finan_ajax.php",
							type: "POST",
							data: {ajax:ajax},
							dataType: "html"
						});
						
						request.done(function(msg) 
						{
							//console.log(msg);
							$("#finan_periOpe").html( msg );
						});
						
						request.fail(function(jqXHR, textStatus) 
						{
							alert( "Request failed: " + textStatus );
						});
					}

					function finan_estaOpe_obte()
					{
						//vars
						ajax="finan_estaOpe_obte";

						//peticion ajax
						var request = $.ajax(
						{
							url: "ajax/finan_ajax.php",
							type: "POST",
							data: {ajax:ajax},
							dataType: "html"
						});
						
						request.done(function(msg) 
						{
							//console.log(msg);
							$("#finan_estaOpe").html( msg );
						});
						
						request.fail(function(jqXHR, textStatus) 
						{
							alert( "Request failed: " + textStatus );
						});
					}	

			//popups


//finan_frmOpe

			//funciones

				//JS

					//***********************************
						//Funciones Modulo Financiero
					//***********************************

					function finan_openBanTem_cre()
					{
						//parametros
						if(document.getElementById('nc_ccId'))
						{
							insTip=document.frmAsigPro.finan_tipDoc;
						}
						else
						{
							insTip=document.frmCosPro.finan_tipDoc;
						}

						tipDocId=new Array();
						ind=0;
						ajax='openBanTem_cre';
						//centTemp=document.getElementById('finan_centTemp').value;
						tipCent=1;

						//evaluar centro existente
						if(document.getElementById('nc_ccId'))
						{
							tipCent=2;
							centTemp=document.getElementById('nc_ccId').value;
						}

						for(i=0;i<insTip.length;i++)
						{
							if(insTip[i].checked)
							{
								tipDocId[ind++]=insTip[i].value;
								insTip[i].checked=false;
							}
						}

						console.log(tipDocId);

						if(tipDocId.length>0)
						{
							
							//peticion ajax
							var request = $.ajax({
							url: "ajax/finan_ajax.php",
							type: "POST",
							data: {tipDocId:tipDocId,ajax:ajax,centTemp:centTemp,tipCent:tipCent},
							dataType: "html"
							});
							
							request.done(function(msg) {
							//document.getElementById('scInventario').value='';
							//var acontenidoAjax = a('#loading').html('');
							$("#finan_detOpe").html( msg );
							finan_iniCalen('finan_fechCli');
							finan_iniCalen('finan_fechDoc');
							finan_iniCalen('finan_fechIni');
							});
							
							request.fail(function(jqXHR, textStatus) {
							alert( "Request failed: " + textStatus );
							});
							
							//alert("Operacion financiera seleccionada");
						}
						else
						{
							alert("Operacion financiera no seleccionada");
						}

					}

					function finan_opeBanTem_obte()
					{
						//parametros
						ajax='opeBanTem_obte';
						//centTemp=document.getElementById('finan_centTemp').value;
						tipCent=1;

						if(document.getElementById('nc_ccId'))
						{
							tipCent=2;
							centTemp=document.getElementById('nc_ccId').value;
						}

						//peticion ajax
						var request = $.ajax({
						url: "ajax/finan_ajax.php",
						type: "POST",
						data: {ajax:ajax,centTemp:centTemp,tipCent:tipCent},
						dataType: "html"
						});
						
						request.done(function(msg) {
						//document.getElementById('scInventario').value='';
						//var acontenidoAjax = a('#loading').html('');
						$("#finan_detOpe").html( msg );
						finan_iniCalen('finan_fechCli');
						finan_iniCalen('finan_fechDoc');
						finan_iniCalen('finan_fechIni');
						});
						
						request.fail(function(jqXHR, textStatus) {
						alert( "Request failed: " + textStatus );
						});
					}

					function finan_opeBanTem_eli(opeBanId)
					{
						//parametros
						opeBanId=opeBanId
						json="opeBanTem_eli";
						tipCent=1;

						if(document.getElementById('nc_ccId'))
						{
							tipCent=2;
							centTemp=document.getElementById('nc_ccId').value;
						}

						//parametros param
						param="opeBanId="+opeBanId;
						param+="&json="+json;
						param+="&tipCent="+tipCent;

						//peticion  json
						$.getJSON('json/finan_json.php?'+param,{format: "json"}, function(data) 
						{
							if(data[0]>0)
							{
								console.log("Operacion bancaria eliminada...!");
								finan_opeBanTem_obte();
							}
							else
							{
								console.log("Operacion bancaria no eliminada...!");
							}
						});
					}

					function finan_iniCalen(finanFech)
					{

						if(document.getElementById('nc_ccId'))
						{
							insChk=document.frmAsigPro.finan_chkOpe;
						}
						else
						{
							insChk=document.frmCosPro.finan_chkOpe;
						}

						for(i=0;i<insChk.length;i++)
						{
							if(insChk[i].value>0)
							{
								Calendario3(finanFech+"_"+insChk[i].value);
							}
						}
					}

					function finan_opeBanTem_actu(opeBanId)
					{
						//parametros
						insMone=document.getElementById('finan_mone_'+opeBanId);
						
						moneId=insMone.options[insMone.selectedIndex].value;
						monto=document.getElementById('finan_mont_'+opeBanId).value;
						fechCli=document.getElementById('finan_fechCli_'+opeBanId).value;
						fechDoc=document.getElementById('finan_fechDoc_'+opeBanId).value;
						opeIdBan=opeBanId;
						json="opeBanTem_actu";
						tipCent=1;
						fechIni=document.getElementById('finan_fechIni_'+opeBanId).value;
						tasAnu=document.getElementById('finan_tasAnu_'+opeBanId).value;
						comisInte=document.getElementById('finan_comisInte_'+opeBanId).value;
						correOpe=document.getElementById('finan_num_'+opeBanId).value;

						//obtener estados de operacion bancaria
						estaVenci=finan_estaCheck('finan_estaVenci_'+opeBanId);
						estaEntre=finan_estaCheck('finan_estaEntre_'+opeBanId);

						console.log(estaVenci);


						if(document.getElementById('nc_ccId'))
						{
							tipCent=2;
						}

						//parametros param
						param="moneId="+moneId;
						param+="&monto="+monto;
						param+="&fechCli="+fechCli;
						param+="&fechDoc="+fechDoc;
						param+="&opeIdBan="+opeIdBan;
						param+="&json="+json;
						param+="&tipCent="+tipCent;
						param+="&fechIni="+fechIni;
						param+="&tasAnu="+tasAnu;
						param+="&comisInte="+comisInte;
						param+="&estaVenci="+estaVenci;
						param+="&estaEntre="+estaEntre;
						param+="&correOpe="+correOpe;


						//peticion json
						$.getJSON('json/finan_json.php?'+param,{format: "json"}, function(data) 
						{
							if(data[0]>0)
							{
								console.log("Operacion actualizada correctamente...!");
								finan_opeBanTem_obte();
							}
							else
							{
								console.log("Operacion no actualizada...!");
							}

						});
					}

					function finan_calcuReno(json)
					{
						//parametros
						ind=0;
						opeIdBan=new Array();
						tipCent=0;
						json=json;
						if(document.getElementById('nc_ccId'))
						{
							insChk=document.frmAsigPro.finan_chkOpe;
							tipCent=2;
						}
						else
						{
							insChk=document.frmCosPro.finan_chkOpe;
							tipCent=1;
						}

						for(i=0;i<insChk.length;i++)
						{
							if(insChk[i].checked)
							{
								opeIdBan[ind++]=insChk[i].value;
								insChk[i].checked=false;
							}
						}

						//peticion ajax
						$.ajax
						({
						    type:"POST",
						    url: 'json/finan_json.php',
						    data:{opeIdBan:opeIdBan,tipCent:tipCent,json:json},
						    dataType: 'json',
						    success: function(data) 
						    {
						    	if(data[0]>0)
						    	{
						    		console.log("Operaciones bancarias calculadas o renovadas correctamente.....!");
						    		finan_opeBanTem_obte();
						    	}
						    	else
						    	{
						    		console.log("Operaciones no calculadas o renovadas");
						    	}
						    }
						});

					}

					function finan_estaCheck(id)
					{
						if(document.getElementById(id).checked)
						{
							val=1
						}
						else
						{
							val=0;
						}
						return val;
					}

					//-----------------o--------------------

					function finan_notiAdju(flag,ruta)
					{
						if(flag>0 && ruta!="")
						{
							$(".elem-gd").notify("formato adjunto correctamente","success");

							//iniciar archivos adjuntos
								//nc_infoAdju_obte();
								finan_adjuOpeXId_obte();

							//limpiar formulario
							document.finan_frmAdju.finan_numDoc.value="";
							document.finan_frmAdju.finan_des.value="";
							document.finan_frmAdju.finan_adjuFile.value="";
						}
						else
						{
							$(".elem-gd").notify("formato no adjunto","error");
						}
					}

					function finan_opeDoc_adju()
					{
							opeId=document.getElementById('finan_opeId').value;

							if(opeId>0)
							{
								document.finan_frmAdju.method='post';
								document.finan_frmAdju.target='finan_iframe';
								document.finan_frmAdju.action='iframe/finan_iframe.php';
								document.finan_frmAdju.finan_iframe_peti.value="finan_opeProy_adju";
								document.finan_frmAdju.finan_opeId_adju.value=opeId;
								document.finan_frmAdju.submit();
							}
							else
							{
								alert("Guardar operacion de proyecto antes de continuar...!");
							}
					}


				//JSON

					function finan_datCentxId()
					{
						//vars
						json="finan_datCentxId";
						centId=document.getElementById('nc_ccId').value;

						//params
						param="json="+json;
						param+="&centId="+centId;

						$.getJSON('json/finan_json.php?'+param,{format: "json"}, function(data) 
						{
							console.log(data);
							if(data.length>0)
							{
								document.getElementById('finan_proye').innerHTML=data[0]['nc_proye'];
								document.getElementById('finan_respo').innerHTML=data[0]['ingRespDes'];
								document.getElementById('finan_cli').innerHTML=data[0]['nc_cli'];
								document.getElementById('finan_mone').innerHTML=data[0]['moneDes'];
								document.getElementById('finan_mont').innerHTML=data[0]['montCoti'];
								document.getElementById('finan_fech').innerHTML=data[0]['fechCli'];
							}
						});
					}

					function finan_opeProye_crear()
					{
						//test
						//console.log("creando operacion proyecto");

						//vars
						idOpeProye=document.getElementById('finan_opeId').value;
						json=gd_peti2Json_ele(idOpeProye,"finan_opeProye_crear","finan_opeProye_actu");
						centId=document.getElementById('nc_ccId').value;

						//params
						param="json="+json;
						param+="&centId="+centId;
						param+="&idOpeProye="+idOpeProye;

						//peticion json
						$.getJSON('json/finan_json.php?'+param,{format: "json"}, function(data) 
						{
							if(data[0]=='ya existe')
							{
								$(".elem-gd").notify("Operacion de proyecto ya existe...!","error");
							}
							else if(data[0]>0 && json=="finan_opeProye_crear")
							{
								$(".elem-gd").notify("Operacion de proyecto generada correctamente...!","success");

								url="index.php";
								param="menu_id=162";
								param+="&menu=finan_frmOpe";
								param+="&id="+data[0];
								gd_direPagParam(url,param);	
							}
							else if(data[0]>0 && json=="finan_opeProye_actu")
							{
								$(".elem-gd").notify("Operacion de proyecto actualizada correctamente...!","success");
							}
							else
							{
								$(".elem-gd").notify("Operacion de proyecto no generada...!","error");		
							}
						});
					}

					function finan_opeProyexId_obte()
					{
						//vars
						json="finan_opeProyexId_obte";
						opeId=document.getElementById('finan_opeId').value;

						//params
						param="json="+json;
						param+="&opeId="+opeId;

						//peticion json
						$.getJSON('json/finan_json.php?'+param,{format: "json"}, function(data) 
						{
							if(data.length>0)
							{
								document.getElementById('finan_numOpe').innerHTML=data[0]['finan_opeProyeCorre'];
								document.getElementById('nc_ccDes').value=data[0]['centDes'];
								document.getElementById('nc_ccId').value=data[0]['cc_centCostId'];
								document.getElementById('finan_estaImg').innerHTML="<img src="+data[0]['estaImg']+" >";
							}
						});
					}

					function finan_opeProye_eli(opeId)
					{
						/* vars */
						opeId=opeId;
						json="finan_opeProye_eli";

						/* params */
						param="json="+json;
						param+="&opeId="+opeId;

						/* peticion json */
						$.getJSON('json/finan_json.php?'+param,{format: "json"}, function(data) 
						{
							if(data[0]==1)
							{
								$(".elem-gd").notify("Operacion de proyecto eliminada","success");
								finan_opeProye_obte();
							}
							else
							{
								$(".elem-gd").notify("Operacion de proyecto contiene operaciones","error");
							}
						});
					}

					function finan_adjuOpe_eli(adjuId)
					{
						//test
						console.log("Eliminando adjunto con id: "+adjuId);

						//vars
						adjuId=adjuId;
						json="finan_adjuOpe_eli";

						//params
						param="adjuId="+adjuId;
						param+="&json="+json;											

						//peticion  json
						$.getJSON('json/finan_json.php?'+param,{format: "json"}, function(data) 
						{
							if(data[0]>0)
							{
								$(".elem-gd").notify("Adjunto eliminado correctamente","success");
								finan_adjuOpeXId_obte();
							}
							else
							{
								$(".elem-gd").notify("Adjunto no eliminado","error ");	
							}
						});
					}

					function finan_opeProye_cerrar()
					{
						/*vars*/
						json="finan_opeProye_cerrar";
						opeId=document.getElementById('finan_opeId').value;

						/*params*/
						param="json="+json;
						param+="&opeId="+opeId;

						/*peticion json*/
						$.getJSON('json/finan_json.php?'+param,{format: "json"}, function(data) 
						{
							if(data[0]>0)
							{
								$(".elem-gd").notify("Operacion de proyecto cerrado correctamente...!","success");
								finan_opeProyexId_obte();
								setTimeout('finan_datCentxId()',1200);
							}
							else
							{
								$(".elem-gd").notify("Operacion de proyecto no cerrado...!","error ");
							}
						});
					}


				//AJAX

					function finan_adjuOpeXId_obte()
					{

						/* vars */
						ajax="finan_adjuOpeXId_obte";
						opeId=document.getElementById('finan_opeId').value;

						//peticion ajax
							var request = $.ajax(
							{
								url: "ajax/finan_ajax.php",
								type: "POST",
								data: {ajax:ajax,opeId:opeId},
								dataType: "html"
							});
							
							request.done(function(msg) 
							{
								//console.log(msg);
								$("#finan_adjuTab").html( msg );
							});
							
							request.fail(function(jqXHR, textStatus) 
							{
								alert( "Request failed: " + textStatus );
							});
					}			

			//popups


