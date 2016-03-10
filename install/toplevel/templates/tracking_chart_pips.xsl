<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  
	xmlns:set="http://exslt.org/sets">

<xsl:output method="html" encoding="utf-8" indent="yes" doctype-system="about:legacy-compat" />

<xsl:template match="students">
  <xsl:param name="bid"><xsl:value-of select="component/value_db" /></xsl:param>
  <xsl:variable name="students" select="student" />

  <div class="head">
  	  <div class="center">
  	  	PIPS Class Statistics
  	  </div>
  	<br />
  
	  <label>PIPS tracking chart:</label>
	  <xsl:value-of select="subject/value" />
	   <xsl:text> </xsl:text>
	  <xsl:value-of select="component/value" />
	  <br />
	  <label>Total students:</label>
	  <xsl:value-of select="count(/students/student)"/>  
  </div>

  <div id="chart" class="center"></div>

  <div class="hidden">
	<table id="graph">
	  <tfoot id="graph_axis">
		<tr>
		  <th> </th>
		  <xsl:for-each select="$students">
			  <xsl:element name="td">
				<xsl:attribute name="id"><xsl:value-of select="id_db/text()" /></xsl:attribute>
				<xsl:value-of select="forename/value/text()" />
				<xsl:text> </xsl:text>
				<xsl:value-of select="surname/value/text()" />
				-
				<xsl:value-of select="registrationgroup/value/text()" />
			  </xsl:element>
		  </xsl:for-each>
		</tr>
	  </tfoot>
	  <tbody id="graph_data1">
		<xsl:call-template name="assrow">
		  <xsl:with-param name="assindex" select="1"/>
		  <xsl:with-param name="bid" select="$bid" />
		  <xsl:with-param name="students" select="$students" />
		  <!--xsl:sort select="assessments/assessement/result/value" data-type="number" order="descending"/-->
		</xsl:call-template>
	  </tbody>
	</table>
  </div>

  <table id="display">
	  <tbody id="data">
		<xsl:call-template name="datarows">
		  <xsl:with-param name="students" select="$students" />
		</xsl:call-template>

	  </tbody>
	</table>
  <div class='spacer'></div>

<hr />

</xsl:template>


<xsl:template name="assrow">
  <xsl:param name="assindex">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="students"></xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(asstable/ass[element='SST']/id_db)+1"/>
  </xsl:variable>
  <xsl:variable name="asses" select="asstable/ass[element='SST']/id_db" />
  <xsl:variable name="assnames" select="asstable/ass[element='SST']/element" />

  <xsl:if test="$assindex &lt; $maxindex">
	<tr>
	  <th scope="row">
		<xsl:value-of select="$assnames[$assindex]" />
	  </th>

	  <xsl:for-each select="$students">
		<xsl:sort select="assessments/assessment[element='SST']/value/value/text()" data-type="number" order="descending"/>
		<td>
		  <xsl:choose>
			<xsl:when test="count(assessments/assessment[id_db=$asses[$assindex]]) &gt; 0">
			  <xsl:value-of select="assessments/assessment[id_db=$asses[$assindex]]/value" />
			</xsl:when>
			<xsl:otherwise>
			  <xsl:text>0</xsl:text>
			</xsl:otherwise>
		  </xsl:choose>
		</td>
	  </xsl:for-each>
	</tr>

	<xsl:call-template name="assrow">
	  <xsl:with-param name="assindex" select="$assindex + 1"/>
	  <xsl:with-param name="bid" select="$bid" />
	  <xsl:with-param name="students" select="$students" />
	</xsl:call-template>
  </xsl:if>

</xsl:template>

<xsl:template name="datarows">
  <xsl:param name="students"></xsl:param>
 
	  <tr>
			<th rowspan="2">Student</th>
			<th colspan="2">
				Standardised Age Score
				<div id="average">Average: </div>
			</th>
			<xsl:for-each select="set:distinct($students/assessments/assessment[element='SSP' or element='SSR' or element='SSM']/element/value)">
				<th rowspan="2">
					<xsl:value-of select="." />
				</th>
			</xsl:for-each>
	  </tr>
	  <tr>
	  	<xsl:for-each select="set:distinct($students/assessments/assessment[element='SST']/element/value)">
	  		<th><xsl:value-of select="." /></th>
	  	</xsl:for-each>
		<th id="axis"></th>
	  </tr>

	  <xsl:for-each select="$students">
		<xsl:sort select="assessments/assessment[element='SST']/value/value/text()" data-type="number" order="descending"/>
		<tr>
			<td>
				<xsl:value-of select="forename/value" />
				<xsl:text> </xsl:text>
				<xsl:value-of select="surname/value" />
				<xsl:text> </xsl:text>
				<xsl:value-of select="registrationgroup/value" /></td>
			<td>
				<xsl:value-of select="assessments/assessment[element='SST']/value" />
			</td>
			<td id="graphicattach-{position()-1}" style="width:50%;padding:0;">
				
			</td>
			<xsl:for-each select="assessments/assessment[element='SSP' or element='SSR' or element='SSM']">
			  <td>
				<xsl:value-of select="value" />
			  </td>
			</xsl:for-each>
		</tr>
	  </xsl:for-each>

</xsl:template>

</xsl:stylesheet>
