<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  
	xmlns:set="http://exslt.org/sets">

<xsl:output method="html" encoding="utf-8" indent="yes" doctype-system="about:legacy-compat" />


<xsl:template match="/">
  <xsl:variable name="mapurl">
	<xsl:text>https://maps.googleapis.com/maps/api/js?key=</xsl:text><xsl:value-of select="admissioncenters/api_key" /><xsl:text>&amp;sensor=false</xsl:text>
  </xsl:variable>

<!--
<xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html></xsl:text>
-->
  <html>
	<head>
	  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	  <style type="text/css">
		html { height: 100% }
		body { height: 100%; margin: 0; padding: 0 }
		#map_canvas { height: 90% }
	  </style>

	  <xsl:element name="script">
		<xsl:attribute name="type">text/javascript</xsl:attribute>
		<xsl:attribute name="src"><xsl:value-of select="$mapurl" /></xsl:attribute>
	  </xsl:element>
<!--
	  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyABCCSux-3CiZxdHVw9h9kAeK7iEbCxFoI&amp;sensor=false"></script>
-->
	  <script type="text/javascript" language="JavaScript" src="../templates/demographic_chart.js" charset="utf-8"></script>
	</head>
	<body onload="initialize();">
	  <div id="map_canvas" style="width:100%; height:90%;"></div>
	</body>


	<xsl:apply-templates select="admissioncenters/admissioncenter">
	</xsl:apply-templates>


  </html>

</xsl:template>




<xsl:template match="admissioncenter">
  <xsl:param name="rownames" select="set:distinct(stats/tables/table/row[name!='' and count(cell)>0]/name)"/>
  <xsl:variable name="schoolid" select="stats/school/id" />
  <div id="map_control" style="width:100%;background-color:#999;">
	<div>
	  <form>
		<xsl:element name="button">
		  <xsl:attribute name="class">control</xsl:attribute>
		  <xsl:attribute name="id">school<xsl:value-of select="$schoolid" /></xsl:attribute>
		  <xsl:attribute name="disabled">disabled</xsl:attribute>
		  <xsl:value-of select="stats/school/value" />
		</xsl:element>

		<xsl:for-each select="$rownames">
		  <xsl:element name="input">
			<xsl:attribute name="onclick">changeOverlays('<xsl:value-of select="$schoolid" /><xsl:value-of select="." />');</xsl:attribute>
			<xsl:attribute name="type">checkbox</xsl:attribute>
			<xsl:attribute name="class">control</xsl:attribute>
			<xsl:attribute name="checked">checked</xsl:attribute>
			<xsl:attribute name="id"><xsl:value-of select="$schoolid" /><xsl:value-of select="." /></xsl:attribute>
			<xsl:value-of select="." />
		  </xsl:element>
		</xsl:for-each>
	  </form>
	</div>
  </div>

  <xsl:call-template name="constructtable">
	<xsl:with-param name="rows" select="set:distinct(stats/tables/table/row[name!='' and count(cell)>0])"/>
  </xsl:call-template>

</xsl:template>





<xsl:template name="constructtable">
  <xsl:param name="rows"></xsl:param>

  <div class="hidden" style="display:none;">
	<xsl:element name="table">
	  <xsl:attribute name="id"><xsl:value-of select="stats/school/id" /></xsl:attribute>
	  <xsl:attribute name="class">hidden</xsl:attribute>
		<tr>
		  <td>
			<xsl:value-of select="stats/school/latlng" />
		  </td>
		</tr>

	  <xsl:call-template name="constructrow">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="rows" select="$rows"/>
		<xsl:with-param name="schoolid" select="stats/school/id"/>
	  </xsl:call-template>

	</xsl:element>
  </div>

</xsl:template>




<xsl:template name="constructrow">
  <xsl:param name="index">0</xsl:param>
  <xsl:param name="rows"></xsl:param>
  <xsl:param name="schoolid"></xsl:param>

  <xsl:variable name="maxindex" select="count($rows)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<xsl:element name="tr">
	  <xsl:attribute name="id"><xsl:value-of select="$schoolid" /><xsl:value-of select="$rows[$index]/name" /></xsl:attribute>

	  <xsl:for-each select="$rows[$index]/cell">
		<td>
		  <xsl:value-of select="." />
		</td>
	  </xsl:for-each>

	</xsl:element>
  
	<xsl:call-template name="constructrow">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="rows" select="$rows"/>
	  <xsl:with-param name="schoolid" select="$schoolid"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>



</xsl:stylesheet>
